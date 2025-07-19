<script setup>

const props = defineProps({
    quote: Object,
    isRefreshingQuote: Boolean,
});

const emit = defineEmits(['refresh']);

const refreshQuote = () => {
    emit('refresh');
};

</script>

<template>

    <div class="mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg">
        <div class="p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium opacity-90">Daily Motivation</span>
                    </div>
                    <blockquote class="text-lg font-medium leading-relaxed mb-3">
                        "{{ quote.content }}"
                    </blockquote>
                    <cite class="text-sm opacity-90">
                        — {{ quote.author }}
                    </cite>
                    <div v-if="quote.tags && quote.tags.length > 0" class="flex flex-wrap gap-1 mt-3">
                        <span v-for="tag in quote.tags" :key="tag"
                            class="px-2 py-1 text-xs font-medium bg-white bg-opacity-20 rounded-full">
                            {{ tag }}
                        </span>
                    </div>
                </div>
                <button @click="refreshQuote"
                    class="ml-4 p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors duration-200"
                    :disabled="isRefreshingQuote" title="Refresh quote">
                    <svg class="w-5 h-5" :class="{ 'animate-spin': isRefreshingQuote }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
