<script setup>
import Counter from '@/Components/Counter.vue';
import { useTaskApi } from '@/Utils/task';


const { total } = await useTaskApi({
    status: 'done',
    published: true,
    per_page: 1
});
</script>

<template>
    <Counter label="Done" :count="total || 0" />
</template>
