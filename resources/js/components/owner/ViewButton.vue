<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Eye } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Props {
    href: string;
    variant?: 'default' | 'ghost' | 'outline';
    size?: 'default' | 'sm' | 'lg' | 'icon';
}

withDefaults(defineProps<Props>(), {
    variant: 'ghost',
    size: 'icon',
});
</script>

<template>
    <Button :variant="variant" :size="size" as-child>
        <Link :href="href">
            <Eye class="h-4 w-4" />
            <span class="sr-only">View</span>
        </Link>
    </Button>
</template>

