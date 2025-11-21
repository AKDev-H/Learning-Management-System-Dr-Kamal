<script setup lang="ts">
import { computed, h } from 'vue';
import {
    useVueTable,
    getCoreRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    flexRender,
    type ColumnDef,
} from '@tanstack/vue-table';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ArrowUpDown } from 'lucide-vue-next';

interface Props<TData> {
    data: TData[];
    columns: ColumnDef<TData>[];
    viewMode?: 'table' | 'card';
}

const props = withDefaults(defineProps<Props<any>>(), {
    viewMode: 'table',
});

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
});
</script>

<template>
    <div class="w-full">
        <div v-if="viewMode === 'table'" class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <div
                                v-if="!header.isPlaceholder"
                                class="flex items-center space-x-2"
                            >
                                <button
                                    v-if="header.column.getCanSort()"
                                    @click="header.column.toggleSorting()"
                                    class="flex items-center gap-2 hover:opacity-70"
                                >
                                    {{
                                        flexRender(
                                            header.column.columnDef.header,
                                            header.getContext(),
                                        )
                                    }}
                                    <ArrowUpDown class="h-4 w-4" />
                                </button>
                                <span v-else>
                                    {{
                                        flexRender(
                                            header.column.columnDef.header,
                                            header.getContext(),
                                        )
                                    }}
                                </span>
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="row.getIsSelected() && 'selected'"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                {{
                                    flexRender(
                                        cell.column.columnDef.cell,
                                        cell.getContext(),
                                    )
                                }}
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell
                            :colspan="columns.length"
                            class="h-24 text-center"
                        >
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="row in table.getRowModel().rows"
                :key="row.id"
                class="rounded-lg border p-4"
            >
                <slot name="card" :row="row" />
            </div>
        </div>
    </div>
</template>

