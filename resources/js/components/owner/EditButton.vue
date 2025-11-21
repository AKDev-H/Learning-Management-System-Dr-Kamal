<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Props {
    href?: string;
    onClick?: () => void;
    variant?: 'default' | 'ghost' | 'outline';
    size?: 'default' | 'sm' | 'lg' | 'icon';
}

withDefaults(defineProps<Props>(), {
    variant: 'ghost',
    size: 'icon',
});

const emit = defineEmits<{
    click: [];
}>();
</script>

<template>
    <Button
        v-if="href"
        :variant="variant"
        :size="size"
        as-child
    >
        <Link :href="href">
            <Pencil class="h-4 w-4" />
            <span class="sr-only">Edit</span>
        </Link>
    </Button>
    <Button
        v-else
        :variant="variant"
        :size="size"
        @click="onClick || emit('click')"
    >
        <Pencil class="h-4 w-4" />
        <span class="sr-only">Edit</span>
    </Button>
</template>


