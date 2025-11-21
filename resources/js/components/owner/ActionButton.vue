<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Component } from 'vue';
import { MoreHorizontal } from 'lucide-vue-next';

interface Action {
    label: string;
    icon?: Component;
    onClick: () => void;
    variant?: 'default' | 'destructive';
    disabled?: boolean;
}

interface Props {
    actions: Action[];
    variant?: 'default' | 'ghost' | 'outline';
    size?: 'default' | 'sm' | 'lg' | 'icon';
}

withDefaults(defineProps<Props>(), {
    variant: 'ghost',
    size: 'icon',
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button :variant="variant" :size="size">
                <MoreHorizontal class="h-4 w-4" />
                <span class="sr-only">Open menu</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem
                v-for="(action, index) in actions"
                :key="index"
                :disabled="action.disabled"
                :class="action.variant === 'destructive' ? 'text-destructive' : ''"
                @click="action.onClick"
            >
                <component v-if="action.icon" :is="action.icon" class="mr-2 h-4 w-4" />
                {{ action.label }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

