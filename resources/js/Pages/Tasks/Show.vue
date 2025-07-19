<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
});

const deleteTask = () => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('tasks.destroy', props.task.id));
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

const formatDate = (dateString) => {
    if (!dateString) return 'Not set';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Task: ${task.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Task Details
                </h2>
                <div class="flex space-x-3">
                    <a
                        :href="route('tasks.edit', task.id)"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Edit Task
                    </a>
                    <a
                        :href="route('dashboard')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Task Header -->
                        <div class="mb-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ task.title }}</h1>
                                    <div class="flex items-center mt-2 space-x-3">
                                        <span
                                            :class="getStatusColor(task.status)"
                                            class="px-3 py-1 text-sm font-medium rounded-full"
                                        >
                                            {{ task.status.replace('-', ' ') }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            Created {{ formatDate(task.created_at) }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    @click="deleteTask"
                                    class="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Task Description -->
                        <div v-if="task.description" class="mb-6">
                            <h3 class="mb-2 text-lg font-medium text-gray-900">Description</h3>
                            <div class="p-4 bg-gray-50 rounded-md">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ task.description }}</p>
                            </div>
                        </div>

                        <!-- Task Information Grid -->
                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                            <!-- Due Date -->
                            <div class="p-4 bg-gray-50 rounded-md">
                                <h4 class="mb-2 text-sm font-medium text-gray-700">Due Date</h4>
                                <p class="text-gray-900">
                                    {{ formatDate(task.due_date) }}
                                </p>
                            </div>

                            <!-- Last Updated -->
                            <div class="p-4 bg-gray-50 rounded-md">
                                <h4 class="mb-2 text-sm font-medium text-gray-700">Last Updated</h4>
                                <p class="text-gray-900">
                                    {{ formatDate(task.updated_at) }}
                                </p>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div v-if="task.tags && task.tags.length > 0" class="mb-6">
                            <h4 class="mb-3 text-sm font-medium text-gray-700">Tags</h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in task.tags"
                                    :key="tag"
                                    class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-full"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>

                        <!-- Task Timeline -->
                        <div class="p-4 bg-gray-50 rounded-md">
                            <h4 class="mb-3 text-sm font-medium text-gray-700">Task Timeline</h4>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">
                                        Task created on {{ formatDate(task.created_at) }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">
                                        Last updated on {{ formatDate(task.updated_at) }}
                                    </span>
                                </div>
                                <div v-if="task.due_date" class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">
                                        Due on {{ formatDate(task.due_date) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a
                                :href="route('dashboard')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                Back to Dashboard
                            </a>
                            <a
                                :href="route('tasks.edit', task.id)"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                Edit Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 