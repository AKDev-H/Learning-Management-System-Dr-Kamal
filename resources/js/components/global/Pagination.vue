<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    PaginationEllipsis,
    PaginationItem,
    PaginationList,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { computed } from 'vue';

interface Props {
    currentPage: number;
    lastPage: number;
    total: number;
    perPage: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    pageChange: [page: number];
}>();

const handlePageChange = (page: number) => {
    if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
        emit('pageChange', page);
    }
};

const pages = computed(() => {
    const total = props.lastPage;
    const current = props.currentPage;
    const delta = 2;
    const range = [];
    const rangeWithDots = [];

    if (total <= 1) {
        return [1];
    }

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(total - 1, current + delta);
        i++
    ) {
        range.push(i);
    }

    if (current - delta > 2) {
        rangeWithDots.push(1, '...');
    } else {
        rangeWithDots.push(1);
    }

    rangeWithDots.push(...range);

    if (current + delta < total - 1) {
        rangeWithDots.push('...', total);
    } else if (total > 1) {
        rangeWithDots.push(total);
    }

    return rangeWithDots;
});
</script>

<template>
    <div class="flex items-center justify-center gap-2">
        <PaginationList class="flex items-center gap-1">
            <PaginationItem>
                <PaginationPrevious
                    :disabled="currentPage === 1"
                    @click="handlePageChange(currentPage - 1)"
                />
            </PaginationItem>

            <template v-for="(pageNum, index) in pages" :key="index">
                <PaginationItem v-if="pageNum !== '...'">
                    <Button
                        :variant="pageNum === currentPage ? 'default' : 'ghost'"
                        size="sm"
                        @click="handlePageChange(pageNum)"
                    >
                        {{ pageNum }}
                    </Button>
                </PaginationItem>
                <PaginationItem v-else>
                    <PaginationEllipsis />
                </PaginationItem>
            </template>

            <PaginationItem>
                <PaginationNext
                    :disabled="currentPage === lastPage"
                    @click="handlePageChange(currentPage + 1)"
                />
            </PaginationItem>
        </PaginationList>
    </div>
</template>

