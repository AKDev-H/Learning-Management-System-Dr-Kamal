<script setup lang="ts">
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Button } from '@/components/ui/button';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Option {
    value: string | number;
    label: string;
}

interface Props {
    modelValue?: string | number;
    options: Option[];
    placeholder?: string;
    searchPlaceholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select option...',
    searchPlaceholder: 'Search...',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | undefined];
}>();

const searchValue = ref('');

const selectedOption = computed(() => {
    return props.options.find((opt) => opt.value === props.modelValue);
});

const displayValue = computed(() => selectedOption.value?.label ?? props.placeholder);

const filteredOptions = computed(() => {
    if (!searchValue.value) {
        return props.options;
    }
    return props.options.filter((option) =>
        option.label.toLowerCase().includes(searchValue.value.toLowerCase()),
    );
});

const handleSelect = (value: string | number) => {
    emit('update:modelValue', value);
    searchValue.value = '';
};
</script>

<template>
    <Combobox
        :model-value="modelValue"
        @update:model-value="handleSelect"
    >
        <ComboboxAnchor as-child>
            <ComboboxTrigger class="w-full">
                <Button variant="outline" class="w-full justify-between">
                    {{ displayValue }}
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </ComboboxTrigger>
        </ComboboxAnchor>
        <ComboboxList>
            <ComboboxInput
                v-model="searchValue"
                :placeholder="searchPlaceholder"
            />
            <ComboboxEmpty>No results found.</ComboboxEmpty>
            <ComboboxItem
                v-for="option in filteredOptions"
                :key="option.value"
                :value="option.value"
            >
                <Check
                    :class="[
                        'mr-2 h-4 w-4',
                        modelValue === option.value ? 'opacity-100' : 'opacity-0',
                    ]"
                />
                {{ option.label }}
            </ComboboxItem>
        </ComboboxList>
    </Combobox>
</template>


