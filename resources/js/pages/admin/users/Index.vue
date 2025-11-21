<script setup lang="ts">
import AdminLayout from '@/layouts/admin/AdminLayout.vue';
import SearchInput from '@/components/global/SearchInput.vue';
import SearchableSelect from '@/components/global/SearchableSelect.vue';
import Pagination from '@/components/global/Pagination.vue';
import ActionButton from '@/components/owner/ActionButton.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useQueryState, parseAsInteger, parseAsString } from '@/composables/useQueryState';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, LayoutGrid, List } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: {
        id: number;
        name: string;
        slug: string;
    } | null;
    is_banned: boolean;
    banned_at: string | null;
    created_at: string;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    roles: Array<{ id: number; name: string; slug: string }>;
    filters: {
        search?: string;
        role_id?: number;
        is_banned?: string;
    };
}

const props = defineProps<Props>();

const [search, setSearch] = useQueryState('search', {
    ...parseAsString.withDefault(''),
    clearOnDefault: true,
});

const [selectedRole, setSelectedRole] = useQueryState('role_id', {
    defaultValue: undefined,
    parse: (value: string) => {
        const parsed = parseInt(value, 10);
        return isNaN(parsed) ? undefined : parsed;
    },
    serialize: (value: number | undefined) => value !== undefined ? String(value) : '',
    clearOnDefault: true,
});

const [selectedBanned, setSelectedBanned] = useQueryState('is_banned', {
    ...parseAsString.withDefault(''),
    clearOnDefault: true,
});

const viewMode = ref<'table' | 'card'>('table');

const roleOptions = computed(() =>
    props.roles.map((role) => ({
        value: role.id,
        label: role.name,
    })),
);

const bannedOptions = [
    { value: '', label: 'All' },
    { value: 'true', label: 'Banned' },
    { value: 'false', label: 'Active' },
];

const handleBan = (userId: number) => {
    router.post(`/admin/users/${userId}/ban`, {}, {
        preserveScroll: true,
    });
};

const handleUnban = (userId: number) => {
    router.post(`/admin/users/${userId}/unban`, {}, {
        preserveScroll: true,
    });
};

const handleResetPassword = (userId: number) => {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        router.post(`/admin/users/${userId}/reset-password`, {}, {
            preserveScroll: true,
        });
    }
};

const [currentPage, setCurrentPage] = useQueryState('page', {
    defaultValue: 1,
    parse: (value: string) => {
        const parsed = parseInt(value, 10);
        return isNaN(parsed) ? 1 : parsed;
    },
    serialize: (value: number) => String(value),
    clearOnDefault: true,
});

const handlePageChange = (page: number) => {
    setCurrentPage(page);
};
</script>

<template>
    <Head title="Users Management" />

    <AdminLayout
        :breadcrumbs="[
            { title: 'Dashboard', href: '/admin/dashboard' },
            { title: 'Users' },
        ]"
    >
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Users Management</h1>
                    <p class="text-muted-foreground">
                        Manage system users, roles, and permissions
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="icon"
                        @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    >
                        <LayoutGrid v-if="viewMode === 'table'" class="h-4 w-4" />
                        <List v-else class="h-4 w-4" />
                    </Button>
                    <Button as-child>
                        <Link href="/admin/users/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Create User
                        </Link>
                    </Button>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Filters</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <SearchInput
                            :model-value="search || ''"
                            placeholder="Search users..."
                            @update:model-value="setSearch"
                        />
                        <SearchableSelect
                            :model-value="selectedRole ?? ''"
                            :options="roleOptions"
                            placeholder="Filter by role..."
                            @update:model-value="(val) => setSelectedRole(val ? Number(val) : null)"
                        />
                        <SearchableSelect
                            :model-value="selectedBanned || ''"
                            :options="bannedOptions"
                            placeholder="Filter by status..."
                            @update:model-value="setSelectedBanned"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardContent class="p-0">
                    <div v-if="viewMode === 'table'" class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="user in users.data"
                                    :key="user.id"
                                >
                                    <TableCell class="font-medium">
                                        {{ user.name }}
                                    </TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">
                                            {{ user.role?.name || 'No Role' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            :variant="user.is_banned ? 'destructive' : 'default'"
                                        >
                                            {{ user.is_banned ? 'Banned' : 'Active' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <ActionButton
                                            :actions="[
                                                {
                                                    label: 'Edit',
                                                    onClick: () => router.visit(`/admin/users/${user.id}/edit`),
                                                },
                                                {
                                                    label: 'View',
                                                    onClick: () => router.visit(`/admin/users/${user.id}`),
                                                },
                                                {
                                                    label: user.is_banned ? 'Unban' : 'Ban',
                                                    onClick: () => (user.is_banned ? handleUnban(user.id) : handleBan(user.id)),
                                                    variant: user.is_banned ? 'default' : 'destructive',
                                                },
                                                {
                                                    label: 'Reset Password',
                                                    onClick: () => handleResetPassword(user.id),
                                                },
                                                {
                                                    label: 'Delete',
                                                    onClick: () => {
                                                        if (confirm('Are you sure you want to delete this user?')) {
                                                            router.delete(`/admin/users/${user.id}`, {
                                                                preserveScroll: true,
                                                            });
                                                        }
                                                    },
                                                    variant: 'destructive',
                                                },
                                            ]"
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div v-else class="grid gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card
                            v-for="user in users.data"
                            :key="user.id"
                            class="p-4"
                        >
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold">{{ user.name }}</h3>
                                    <Badge
                                        :variant="user.is_banned ? 'destructive' : 'default'"
                                    >
                                        {{ user.is_banned ? 'Banned' : 'Active' }}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ user.email }}
                                </p>
                                <Badge variant="secondary">
                                    {{ user.role?.name || 'No Role' }}
                                </Badge>
                                <div class="flex justify-end pt-2">
                                    <ActionButton
                                        :actions="[
                                            {
                                                label: 'Edit',
                                                onClick: () => router.visit(`/admin/users/${user.id}/edit`),
                                            },
                                            {
                                                label: 'View',
                                                onClick: () => router.visit(`/admin/users/${user.id}`),
                                            },
                                            {
                                                label: user.is_banned ? 'Unban' : 'Ban',
                                                onClick: () => (user.is_banned ? handleUnban(user.id) : handleBan(user.id)),
                                                variant: user.is_banned ? 'default' : 'destructive',
                                            },
                                            {
                                                label: 'Reset Password',
                                                onClick: () => handleResetPassword(user.id),
                                            },
                                            {
                                                label: 'Delete',
                                                onClick: () => {
                                                    if (confirm('Are you sure you want to delete this user?')) {
                                                        router.delete(`/admin/users/${user.id}`, {
                                                            preserveScroll: true,
                                                        });
                                                    }
                                                },
                                                variant: 'destructive',
                                            },
                                        ]"
                                    />
                                </div>
                            </div>
                        </Card>
                    </div>
                </CardContent>
            </Card>

            <Pagination
                :current-page="(currentPage ?? users.current_page)"
                :last-page="users.last_page"
                :total="users.total"
                :per-page="users.per_page"
                @page-change="handlePageChange"
            />
        </div>
    </AdminLayout>
</template>

