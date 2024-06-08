import { useQuery } from '@tanstack/vue-query';
import { ref } from 'vue';
import { useDebounce } from "@vueuse/core";

export function useTasks(params = {}) {
    const search = ref(params?.search);
    const searchDebounced = useDebounce(search, 200);
    const status = ref(params?.status);
    const sort  = ref(params?.sort || 'created_at');
    const draft = ref(params?.draft || false);
    const page = ref(params?.page || 1);
    const perPage = ref(params?.per_page || 10);

    const get = async () => {
        const response = await axios.get(route('api.tasks.index', {
            filter: {
                title: searchDebounced.value,
                status: status.value,
                draft: draft.value
            },
            sort: sort.value,
            include: 'status,images',
            page: page.value,
            per_page: perPage.value
        }));

        return response?.data?.data || [];
    }

    const { isPending, isFetching, isError, data, error } = useQuery({
        queryKey: [{ status, sort, draft, searchDebounced, page, perPage }],
        queryFn: get,
    });

    return {
        status,
        sort,
        data,
        isPending,
        isFetching,
        isError,
        error,
    };
}
