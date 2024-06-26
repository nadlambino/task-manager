<script setup>
import { useTaskApi, useTaskStore } from '@/Utils/task';
import { Link } from '@inertiajs/vue3';
import PaginatedTable from '@/Components/Table/PaginatedTable.vue';

const props = defineProps({
    headers: {
        type: Array,
        default: () => [
            { key: 'status.name', label: 'Status', class: 'uppercase text-xs font-bold' },
            { key: 'title', label: 'Title', class: 'text-xs' },
            { key: 'created_at', label: 'Created At', class: 'text-xs' },
            { key: 'updated_at', label: 'Updated At', class: 'text-xs' },
        ],
    },
    status: {
        default: null,
    },
    search: {
        default: null,
    },
    perPage: {
        type: Number,
        default: 10,
    },
    trashed: {
        default: null,
    },
    published: {
        default: null,
    },
    deletable: {
        type: Boolean,
        default: false
    },
    restorable: {
        type: Boolean,
        default: false
    },
    editable: {
        type: Boolean,
        default: false
    },
    publishable: {
        type: Boolean,
        default: false
    }
});

const taskStore = useTaskStore();

const {
    data,
    total,
    isRequesting,
    isEmpty,
    hasNextPage,
    refetch,
    next,
    destroy,
    restore,
    update
} = await useTaskApi({
    status: props.status || taskStore.status,
    search: props.search || taskStore.search,
    per_page: props.perPage || taskStore.perPage,
    trashed: props.trashed,
    published: props.published
});
</script>

<template>
    <PaginatedTable :data="data?.pages" :headers="headers" :total="total" :is-empty="isEmpty" :is-requesting="isRequesting" :has-next-page="hasNextPage" label="tasks" @next="next">
        <template #actions="{ row }">
            <div class="flex gap-2">
                <Link v-if="editable" :href="route('tasks.edit', row.id)" class="bg-primary hover:bg-primary/80 text-white uppercase text-xs font-bold py-1 px-2 rounded">Edit</Link>
                <button v-if="publishable" @click="() => update(row.id, { publish: true }).then(refetch)" class="bg-green-500 hover:bg-green-400 text-white uppercase text-xs font-bold py-1 px-2 rounded">Publish</button>
                <button v-if="deletable" @click="() => destroy(row.id).then(refetch)" class="bg-red-500 hover:bg-red-400 text-white uppercase text-xs font-bold py-1 px-2 rounded">Delete</button>
                <button v-if="restorable" @click="() => restore(row.id).then(refetch)" class="bg-green-500 hover:bg-green-400 text-white uppercase text-xs font-bold py-1 px-2 rounded">Restore</button>
            </div>
        </template>
    </PaginatedTable>
</template>
