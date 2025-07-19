<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import QuoteShow from '@/Pages/Quote/Show.vue';

const props = defineProps({
    tasks: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} })
    },
    search: {
        type: String,
        default: ''
    },
    statusFilter: {
        type: String,
        default: ''
    },
    tagFilters: {
        type: Array,
        default: () => []
    },
    availableTags: {
        type: Array,
        default: () => []
    },
    quote: {
        type: Object,
        default: () => ({
            content: '',
            author: '',
            tags: [],
            success: false
        })
    }
});

const searchQuery = ref(props.search);
const statusFilter = ref(props.statusFilter);
const tagFilters = ref(props.tagFilters);
const showTagDropdown = ref(false);
const tagSearchQuery = ref('');
const currentQuote = ref(props.quote);
const isRefreshingQuote = ref(false);

// Pagination state
const currentPage = ref(props.tasks.meta?.current_page || 1);
const perPage = ref(props.tasks.meta?.per_page || 10);

const updateTaskStatus = (taskId, newStatus) => {
    router.patch(route('tasks.update-status', taskId), {
        status: newStatus
    });
};

const deleteTask = (taskId) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('tasks.destroy', taskId));
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'in-progress': return 'bg-blue-100 text-blue-800';
        case 'completed': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Watch for search query changes and update the URL
watch(searchQuery, (newValue) => {
    updateFilters();
}, { debounce: 300 });

// Watch for filter changes
watch(statusFilter, () => {
    updateFilters();
});

watch(tagFilters, () => {
    updateFilters();
}, { deep: true });

const updateFilters = () => {
    const params = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value) params.status = statusFilter.value;
    if (tagFilters.value.length > 0) params.tags = tagFilters.value;
    if (perPage.value !== 10) params.per_page = perPage.value;

    // Reset to first page when filters change
    currentPage.value = 1;

    router.get(route('dashboard'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const clearSearch = () => {
    searchQuery.value = '';
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    tagFilters.value = [];
    tagSearchQuery.value = '';
    currentPage.value = 1;
};

// Pagination functions
const goToPage = (page) => {
    if (page < 1 || page > props.tasks.meta?.last_page) return;

    const params = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value) params.status = statusFilter.value;
    if (tagFilters.value.length > 0) params.tags = tagFilters.value;
    if (perPage.value !== 10) params.per_page = perPage.value;
    if (page > 1) params.page = page;

    currentPage.value = page;

    router.get(route('dashboard'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const changePerPage = (newPerPage) => {
    perPage.value = newPerPage;
    currentPage.value = 1;
    updateFilters();
};

const toggleTag = (tag) => {
    const index = tagFilters.value.indexOf(tag);
    if (index > -1) {
        // Remove tag
        tagFilters.value = tagFilters.value.filter(t => t !== tag);
    } else {
        // Add tag
        tagFilters.value = [...tagFilters.value, tag];
    }
};

const isTagSelected = (tag) => {
    return tagFilters.value.includes(tag);
};

// Filter available tags based on search query
const filteredAvailableTags = computed(() => {
    if (!tagSearchQuery.value) {
        return props.availableTags;
    }
    return props.availableTags.filter(tag =>
        tag.toLowerCase().includes(tagSearchQuery.value.toLowerCase())
    );
});

// Pagination computed properties
const paginationInfo = computed(() => {

    const meta = props.tasks || {};
    return {
        currentPage: meta.current_page || 1,
        lastPage: meta.last_page || 1,
        perPage: meta.per_page || 10,
        total: meta.total || 0,
        from: meta.from || 0,
        to: meta.to || 0,
        hasMorePages: meta.current_page < meta.last_page,
        hasPreviousPages: meta.current_page > 1
    };
});

const paginationLinks = computed(() => {
    const links = props.tasks.links || [];
    // Filter out Previous and Next links, only keep page numbers
    return links.filter(link => {
        const label = link.label;
        return label !== '&laquo; Previous' &&
            label !== 'Next &raquo;' &&
            label !== 'Previous' &&
            label !== 'Next' &&
            label !== '...';
    });
});

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = event.target.closest('.tag-dropdown');
    if (!dropdown) {
        showTagDropdown.value = false;
        tagSearchQuery.value = ''; // Clear search when closing dropdown
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const refreshQuote = async () => {
    isRefreshingQuote.value = true;
    try {
        // Get CSRF token from meta tag or use empty string as fallback
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(route('api.quotes.refresh'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });

        if (response.ok) {
            const responseData = await response.json();
            if (responseData.success) {
                currentQuote.value = responseData.data;
            } else {
                alert('Failed to refresh quote: ' + responseData.message);
                console.error('Failed to refresh quote:', responseData.message);
            }
        } else {
            alert('Failed to refresh quote: HTTP ' + response.status);
            console.error('Failed to refresh quote: HTTP', response.status);
        }
    } catch (error) {
        console.error('Failed to refresh quote:', error);
        alert('Failed to refresh quote: Network error');
    } finally {
        isRefreshingQuote.value = false;
    }
};

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Motivational Quote Section -->
                <QuoteShow :quote="currentQuote" @refresh="refreshQuote" :isRefreshingQuote="isRefreshingQuote" />

                <!-- Search and Filters Section -->
                <div class="mb-6  bg-white shadow-sm sm:rounded-lg">

                    <div class="p-6">

                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                                Task Manager Dashboard
                            </h2>
                            <a :href="route('tasks.create')"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Create New Task
                            </a>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-4 mt-4">
                            <label for="search" class="sr-only">Search tasks</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input id="search" v-model="searchQuery" type="text"
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Search tasks by title, status, or tags..." />
                                <div v-if="searchQuery" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button @click="clearSearch" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Status Filter -->
                            <div class="flex-1 min-w-0">
                                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter by Status
                                </label>
                                <select id="status-filter" v-model="statusFilter"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <!-- Tag Filter -->
                            <div class="flex-1 min-w-0 tag-dropdown">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter by Tags
                                </label>
                                <div class="relative">
                                    <button @click="showTagDropdown = !showTagDropdown" type="button"
                                        class="block w-full px-3 py-2 text-left border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white">
                                        <span v-if="tagFilters.length === 0" class="text-gray-500">
                                            Select tags...
                                        </span>
                                        <span v-else class="text-gray-900">
                                            {{ tagFilters.length }} tag{{ tagFilters.length !== 1 ? 's' : '' }} selected
                                        </span>
                                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Tag Dropdown -->
                                    <div v-if="showTagDropdown"
                                        class="absolute z-99999 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-120 overflow-auto">
                                        <div class="p-2">
                                            <div v-if="availableTags.length === 0"
                                                class="px-3 py-2 text-sm text-gray-500">
                                                No tags available
                                            </div>
                                            <div v-else>
                                                <!-- Tag Search Input -->
                                                <div class="mb-2 relative">
                                                    <input v-model="tagSearchQuery" type="text"
                                                        placeholder="Search tags..."
                                                        class="w-full px-3 py-2 pr-8 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                                                    <button v-if="tagSearchQuery" @click="tagSearchQuery = ''"
                                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Select All / Clear All -->
                                                <div
                                                    class="flex justify-between items-center px-3 py-2 border-b border-gray-200 mb-2">
                                                    <div class="flex space-x-2">
                                                        <button @click="tagFilters = [...availableTags]"
                                                            class="text-xs text-blue-600 hover:text-blue-800">
                                                            Select All
                                                        </button>
                                                        <button @click="tagFilters = []"
                                                            class="text-xs text-gray-600 hover:text-gray-800">
                                                            Clear All
                                                        </button>
                                                    </div>
                                                    <div v-if="tagSearchQuery" class="text-xs text-gray-500">
                                                        {{ filteredAvailableTags.length }} of {{ availableTags.length }}
                                                        tags
                                                    </div>
                                                </div>
                                                <div class="space-y-1 h-200 overflow-auto">
                                                    <div v-if="filteredAvailableTags.length === 0"
                                                        class="px-3 py-2 text-sm text-gray-500">
                                                        No tags found matching "{{ tagSearchQuery }}"
                                                    </div>
                                                    <label v-for="tag in filteredAvailableTags" :key="tag"
                                                        class="flex items-center px-3 py-2 text-sm hover:bg-gray-100 rounded cursor-pointer">
                                                        <input type="checkbox" :checked="isTagSelected(tag)"
                                                            @change="toggleTag(tag)"
                                                            class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                        <span class="flex-1">{{ tag }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Clear Filters Button -->
                            <div class="flex items-end mt-5">
                                <button @click="clearFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Clear Filters
                                </button>
                            </div>
                        </div>

                        <!-- Quick Status Filters -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Filters:</label>
                            <div class="flex flex-wrap gap-2">
                                <button @click="statusFilter = ''" :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full border',
                                    !statusFilter
                                        ? 'bg-blue-100 text-blue-800 border-blue-200'
                                        : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
                                ]">
                                    All
                                </button>
                                <button @click="statusFilter = 'pending'" :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full border',
                                    statusFilter === 'pending'
                                        ? 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                        : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
                                ]">
                                    Pending
                                </button>
                                <button @click="statusFilter = 'in-progress'" :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full border',
                                    statusFilter === 'in-progress'
                                        ? 'bg-blue-100 text-blue-800 border-blue-200'
                                        : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
                                ]">
                                    In Progress
                                </button>
                                <button @click="statusFilter = 'completed'" :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full border',
                                    statusFilter === 'completed'
                                        ? 'bg-green-100 text-green-800 border-green-200'
                                        : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
                                ]">
                                    Completed
                                </button>
                            </div>
                        </div>

                        <!-- Active Filters Summary -->
                        <div v-if="searchQuery || statusFilter || tagFilters.length > 0"
                            class="mt-4 p-3 bg-blue-50 rounded-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-if="searchQuery"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                            Search: "{{ searchQuery }}"
                                            <button @click="clearSearch" class="ml-1 text-blue-500 hover:text-blue-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </span>
                                        <span v-if="statusFilter"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                            Status: {{ statusFilter.replace('-', ' ') }}
                                            <button @click="statusFilter = ''"
                                                class="ml-1 text-blue-500 hover:text-blue-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </span>
                                        <span v-for="tag in tagFilters" :key="tag"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                            Tag: {{ tag }}
                                            <button @click="toggleTag(tag)"
                                                class="ml-1 text-blue-500 hover:text-blue-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <button @click="clearFilters" class="text-sm text-blue-600 hover:text-blue-800">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Results Counter -->
                        <div class="mt-4 text-sm text-gray-500">
                            Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }}
                            task{{ paginationInfo.total !== 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">All Tasks</h3>

                        <div v-if="tasks.data.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No tasks found. Create your first task!</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="task in tasks.data" :key="task.id"
                                class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-lg font-medium text-gray-900">{{ task.title }}</h4>
                                            <span :class="getStatusColor(task.status)"
                                                class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ task.status.replace('-', ' ') }}
                                            </span>
                                        </div>

                                        <p v-if="task.description" class="mt-2 text-gray-600">
                                            {{ task.description }}
                                        </p>

                                        <div class="flex items-center mt-3 space-x-4 text-sm text-gray-500">
                                            <span v-if="task.due_date">
                                                Due: {{ new Date(task.due_date).toLocaleDateString() }}
                                            </span>
                                            <span>
                                                Created: {{ new Date(task.created_at).toLocaleDateString() }}
                                            </span>
                                        </div>

                                        <!-- Tags -->
                                        <div v-if="task.tags && task.tags.length > 0" class="flex flex-wrap mt-2 gap-1">
                                            <span v-for="tag in task.tags" :key="tag"
                                                class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                                {{ tag }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <!-- Status Update -->
                                        <select :value="task.status"
                                            @change="updateTaskStatus(task.id, $event.target.value)"
                                            class="px-8 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <option value="pending">Pending</option>
                                            <option value="in-progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        </select>

                                        <!-- View Button -->
                                        <a :href="route('tasks.show', task.id)"
                                            class="px-3 py-1 text-sm text-green-600 hover:text-green-800">
                                            View
                                        </a>

                                        <!-- Edit Button -->
                                        <a :href="route('tasks.edit', task.id)"
                                            class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800">
                                            Edit
                                        </a>

                                        <!-- Delete Button -->
                                        <button @click="deleteTask(task.id)"
                                            class="px-3 py-1 text-sm text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <div v-if="paginationInfo.total > 0" class="mt-6">
                            <div class="flex items-center justify-between">
                                <!-- Per Page Selector -->
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-700">Show:</label>
                                    <select :value="paginationInfo.perPage" @change="changePerPage($event.target.value)"
                                        class="px-8 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span class="text-sm text-gray-700">per page</span>
                                </div>

                                <!-- Pagination Info -->
                                <div class="text-sm text-gray-700">
                                    Page {{ paginationInfo.currentPage }} of {{ paginationInfo.lastPage }}
                                </div>

                                <!-- Pagination Navigation -->
                                <div class="flex items-center space-x-1">
                                    <!-- Previous Page -->
                                    <button @click="goToPage(paginationInfo.currentPage - 1)"
                                        :disabled="!paginationInfo.hasPreviousPages"
                                        class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Previous
                                    </button>

                                    <!-- Page Numbers -->
                                    <template v-for="link in paginationLinks" :key="link.label">
                                        <button v-if="link.url && !link.active" @click="goToPage(parseInt(link.label))"
                                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                            {{ link.label }}
                                        </button>
                                        <span v-else-if="link.active"
                                            class="px-3 py-1 text-sm bg-blue-500 text-white border border-blue-500 rounded-md">
                                            {{ link.label }}
                                        </span>
                                    </template>

                                    <!-- Next Page -->
                                    <button @click="goToPage(paginationInfo.currentPage + 1)"
                                        :disabled="!paginationInfo.hasMorePages"
                                        class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
