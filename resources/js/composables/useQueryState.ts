import { router, usePage } from '@inertiajs/vue3';
import { ref, watch, type Ref } from 'vue';

interface QueryStateOptions<T> {
    defaultValue?: T;
    parse?: (value: string) => T;
    serialize?: (value: T) => string;
    history?: 'push' | 'replace';
    shallow?: boolean;
    clearOnDefault?: boolean;
}

const defaultOptions: Required<
    Omit<QueryStateOptions<any>, 'parse' | 'serialize'>
> = {
    defaultValue: undefined,
    history: 'push',
    shallow: true,
    clearOnDefault: false,
};

export function useQueryState<T = string>(
    key: string,
    options: QueryStateOptions<T> = {},
) {
    const page = usePage();
    const mergedOptions = { ...defaultOptions, ...options };

    const getValueFromUrl = (): T | undefined => {
        if (typeof window === 'undefined') {
            return mergedOptions.defaultValue;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const value = urlParams.get(key);

        if (value === null) {
            return mergedOptions.defaultValue;
        }

        if (options.parse) {
            try {
                return options.parse(value);
            } catch {
                return mergedOptions.defaultValue;
            }
        }

        return value as T;
    };

    const state = ref<T | undefined>(getValueFromUrl());

    const serializeValue = (value: T | undefined | null): string | null => {
        if (value === undefined || value === null) {
            return null;
        }

        if (options.serialize) {
            return options.serialize(value);
        }

        return String(value);
    };

    const updateUrl = (newValue: T | null | undefined) => {
        if (typeof window === 'undefined') {
            return;
        }

        const currentParams = new URLSearchParams(window.location.search);
        const serialized = serializeValue(newValue as T);

        if (serialized === null || serialized === '') {
            currentParams.delete(key);
        } else {
            currentParams.set(key, serialized);
        }

        const queryString = currentParams.toString();
        const newUrl = queryString
            ? `${window.location.pathname}?${queryString}`
            : window.location.pathname;

        router.get(
            newUrl,
            {},
            {
                preserveState: mergedOptions.shallow,
                preserveScroll: true,
                replace: mergedOptions.history === 'replace',
            },
        );
    };

    const setValue = (
        value: T | null | undefined | ((prev: T | undefined) => T),
    ) => {
        let newValue: T | null | undefined;

        if (typeof value === 'function') {
            newValue = (value as (prev: T | undefined) => T)(state.value);
        } else {
            newValue = value;
        }

        if (
            mergedOptions.clearOnDefault &&
            newValue === mergedOptions.defaultValue
        ) {
            newValue = null;
        }

        state.value = newValue as T | undefined;
        updateUrl(newValue);
    };

    watch(
        () => page.url,
        () => {
            const urlValue = getValueFromUrl();
            if (urlValue !== state.value) {
                state.value = urlValue;
            }
        },
        { immediate: true },
    );

    return [state, setValue] as const;
}

export function useQueryStates<T extends Record<string, any>>(
    keys: Record<keyof T, QueryStateOptions<any>>,
) {
    const states: Record<string, any> = {};
    const setters: Record<string, any> = {};

    Object.entries(keys).forEach(([key, options]) => {
        const [state, setValue] = useQueryState(key, options);
        states[key] = state;
        setters[key] = setValue;
    });

    return [states, setters] as const;
}

export const parseAsInteger = {
    withDefault: (defaultValue: number) => ({
        defaultValue,
        parse: (value: string) => {
            const parsed = parseInt(value, 10);
            return isNaN(parsed) ? defaultValue : parsed;
        },
        serialize: (value: number) => String(value),
    }),
    withOptions: (
        options: QueryStateOptions<number> & { defaultValue: number },
    ) => ({
        ...options,
        parse: (value: string) => {
            const parsed = parseInt(value, 10);
            return isNaN(parsed) ? options.defaultValue : parsed;
        },
        serialize: (value: number) => String(value),
    }),
};

export const parseAsString = {
    withDefault: (defaultValue: string) => ({
        defaultValue,
        parse: (value: string) => value,
        serialize: (value: string) => value,
    }),
    withOptions: (
        options: QueryStateOptions<string> & { defaultValue: string },
    ) => ({
        ...options,
        parse: (value: string) => value,
        serialize: (value: string) => value,
    }),
};

export const parseAsBoolean = {
    withDefault: (defaultValue: boolean) => ({
        defaultValue,
        parse: (value: string) => value === 'true',
        serialize: (value: boolean) => String(value),
    }),
    withOptions: (
        options: QueryStateOptions<boolean> & { defaultValue: boolean },
    ) => ({
        ...options,
        parse: (value: string) => value === 'true',
        serialize: (value: boolean) => String(value),
    }),
};

interface FilterOptions {
    [key: string]: string | number | boolean | undefined;
}

export function useFilter(initialFilters: FilterOptions = {}) {
    const filters: Record<string, Ref<any>> = {};
    const setters: Record<string, (value: any) => void> = {};

    Object.entries(initialFilters).forEach(([key, initialValue]) => {
        const [state, setValue] = useQueryState(key, {
            defaultValue: initialValue,
            clearOnDefault: true,
        });
        filters[key] = state;
        setters[key] = setValue;
    });

    const updateFilter = (
        key: string,
        value: string | number | boolean | undefined,
    ) => {
        if (setters[key]) {
            setters[key](value);
        } else {
            const [state, setValue] = useQueryState(key, {
                defaultValue: value,
                clearOnDefault: true,
            });
            filters[key] = state;
            setters[key] = setValue;
        }
    };

    const applyFilters = (
        url: string,
        additionalParams: Record<string, any> = {},
    ) => {
        const params = new URLSearchParams();

        Object.entries(filters).forEach(([key, stateRef]) => {
            const value = stateRef.value;
            if (value !== undefined && value !== null && value !== '') {
                params.set(key, String(value));
            }
        });

        Object.entries(additionalParams).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                params.set(key, String(value));
            }
        });

        const queryString = params.toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;

        router.get(
            fullUrl,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const resetFilters = () => {
        Object.keys(setters).forEach((key) => {
            setters[key](null);
        });
    };

    const getQueryParams = (additionalParams: Record<string, any> = {}) => {
        const params = new URLSearchParams();

        Object.entries(filters).forEach(([key, stateRef]) => {
            const value = stateRef.value;
            if (value !== undefined && value !== null && value !== '') {
                params.set(key, String(value));
            }
        });

        Object.entries(additionalParams).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                params.set(key, String(value));
            }
        });

        return params.toString();
    };

    return {
        filters,
        updateFilter,
        applyFilters,
        resetFilters,
        getQueryParams,
    };
}
