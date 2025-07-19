<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
});

const form = useForm({
    title: props.task.title,
    description: props.task.description || '',
    status: props.task.status,
    due_date: props.task.due_date || '',
    tags: props.task.tags || []
});

const newTag = ref('');

const addTag = () => {
    if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
        form.tags.push(newTag.value.trim());
        newTag.value = '';
    }
};

const removeTag = (index) => {
    form.tags.splice(index, 1);
};

const updateTask = () => {
    form.put(route('tasks.update', props.task.id), {
        onSuccess: () => {
            // Success message will be handled by the controller
        }
    });
};
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Task
                </h2>
                <a
                    :href="route('dashboard')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Back to Dashboard
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-6 text-lg font-medium text-gray-900">
                            Edit Task: {{ task.title }}
                        </h3>

                        <form @submit.prevent="updateTask" class="space-y-6">
                            <!-- Title Field -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Title *
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter task title"
                                />
                                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter task description (optional)"
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <!-- Status and Due Date Fields -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Status Field -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">
                                        Status *
                                    </label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        required
                                        class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in-progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.status }}
                                    </div>
                                </div>

                                <!-- Due Date Field -->
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700">
                                        Due Date
                                    </label>
                                    <input
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="date"
                                        class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <div v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.due_date }}
                                    </div>
                                                            </div>
                        </div>

                        <!-- Tags Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tags
                            </label>
                            <div class="mt-1">
                                <div class="flex items-center space-x-2">
                                    <input
                                        v-model="newTag"
                                        @keyup.enter="addTag"
                                        type="text"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Add a tag and press Enter"
                                    />
                                    <button
                                        @click="addTag"
                                        type="button"
                                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        Add
                                    </button>
                                </div>
                                
                                <!-- Tags Display -->
                                <div v-if="form.tags.length > 0" class="flex flex-wrap mt-2 gap-2">
                                    <span
                                        v-for="(tag, index) in form.tags"
                                        :key="index"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-full"
                                    >
                                        {{ tag }}
                                        <button
                                            @click="removeTag(index)"
                                            type="button"
                                            class="ml-1 text-blue-500 hover:text-blue-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Task Information Display -->
                            <div class="p-4 bg-gray-50 rounded-md">
                                <h4 class="mb-3 text-sm font-medium text-gray-700">Task Information</h4>
                                <div class="grid grid-cols-1 gap-3 text-sm text-gray-600 md:grid-cols-2">
                                    <div>
                                        <span class="font-medium">Created:</span>
                                        {{ new Date(task.created_at).toLocaleDateString() }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Last Updated:</span>
                                        {{ new Date(task.updated_at).toLocaleDateString() }}
                                    </div>
                                    <div v-if="task.due_date">
                                        <span class="font-medium">Current Due Date:</span>
                                        {{ new Date(task.due_date).toLocaleDateString() }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Current Status:</span>
                                        <span
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': task.status === 'pending',
                                                'bg-blue-100 text-blue-800': task.status === 'in-progress',
                                                'bg-green-100 text-green-800': task.status === 'completed'
                                            }"
                                            class="px-2 py-1 ml-1 text-xs font-medium rounded-full"
                                        >
                                            {{ task.status.replace('-', ' ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a
                                    :href="route('dashboard')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Updating...' : 'Update Task' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 