<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <h1 class="text-2xl font-bold mb-4">Bulk Thumbnail Requests</h1>

            <form @submit.prevent="submitUrls" class="mb-6">
            <label class="block mb-2 font-semibold" for="image_urls">
                Paste image URLs (one per line) — Max {{ limits?.[tier]?.limit ?? 0 }}
            </label>

            <textarea
                id="image_urls"
                v-model="form.image_urls"
                rows="8"
                class="w-full border rounded p-2"
                placeholder="https://example.com/image1.jpg"
                :disabled="form.processing"
            ></textarea>

            <div v-if="form.errors.image_urls" class="text-red-600 mt-2">
                {{ form.errors.image_urls }}
            </div>

            <button
                type="submit"
                class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-60"
                :disabled="form.processing"
            >
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>

            <p v-if="successMessage" class="text-green-600 mt-2">{{ successMessage }}</p>
            <p v-if="errorMessage" class="text-red-600 mt-2">{{ errorMessage }}</p>
            </form>

            <div class="mb-4 flex items-center gap-3">
            <label class="mr-2 font-semibold" for="filter-status">Filter by status:</label>
            <select id="filter-status" v-model="localFilters.status" @change="applyFilters"
                    class="border px-2 py-1 rounded">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="processed">Processed</option>
                <option value="failed">Failed</option>
            </select>
            </div>

            <!-- Results Table -->
            <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-left">Image URL</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Processed At</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in itemsSafe.data" :key="item.id">
                    <td class="border border-gray-300 px-3 py-2 break-words max-w-xs">
                    <a :href="item.image_url" target="_blank" class="text-blue-600 underline">
                        {{ item.image_url }}
                    </a>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 capitalize">
                    <span :class="{
                        'text-yellow-600': item.status === 'pending',
                        'text-green-600': item.status === 'processed',
                        'text-red-600': item.status === 'failed'
                    }">
                        {{ item.status }}
                    </span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2">
                    {{ item.processed_at ? new Date(item.processed_at).toLocaleString() : '-' }}
                    </td>
                </tr>

                <tr v-if="itemsSafe.data.length === 0">
                    <td colspan="3" class="text-center py-6 text-gray-500">No results found.</td>
                </tr>
                </tbody>
            </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center items-center gap-2 flex-wrap">
            <button
                class="px-3 py-1 border rounded disabled:opacity-50"
                :disabled="!itemsSafe.prev_page_url"
                @click="goToPage(itemsSafe.current_page - 1)"
            >
                Prev
            </button>

            <!-- show pages from links if available, else show current page -->
            <template v-if="itemsSafe.links && itemsSafe.links.length">
                <button
                v-for="link in itemsSafe.links"
                :key="link.label + (link.url || '')"
                class="px-3 py-1 border rounded"
                :class="{ 'bg-gray-200': link.active }"
                :disabled="!link.url"
                @click.prevent="link.url && goToUrl(link.url)"
                v-html="link.label"
                />
            </template>

            <span v-else class="px-3 py-1 border rounded">{{ itemsSafe.current_page }}</span>

            <button
                class="px-3 py-1 border rounded disabled:opacity-50"
                :disabled="!itemsSafe.next_page_url"
                @click="goToPage(itemsSafe.current_page + 1)"
            >
                Next
            </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Bulk Requests',
        href: '/bulk-requests',
    },
];

const props = defineProps({
  items: { type: Object, default: () => null },
  filters: { type: Object, default: () => ({}) },
  tier: { type: String, default: '' },
  limits: { type: Object, default: () => ({}) },
})

const form = useForm({
  image_urls: ''
})

const successMessage = ref('')
const errorMessage = ref('')

const localFilters = ref({ ...props.filters })

const itemsSafe = computed(() => {
  if (!props.items) {
    return {
      data: [],
      current_page: 1,
      prev_page_url: null,
      next_page_url: null,
      links: []
    }
  }
  return props.items
})

function submitUrls() {
  successMessage.value = ''
  errorMessage.value = ''

  form.post('/bulk-requests', {
    preserveScroll: true,
    onSuccess: (page) => {
      successMessage.value = 'Request submitted successfully.'
      form.reset('image_urls')
    },
    onError: (errors) => {
      errorMessage.value = ''
    }
  })
}

function applyFilters() {
  router.get('/bulk-requests', localFilters.value, {
    preserveState: true,
    replace: true,
    only: ['items', 'filters']
  })
}

function goToPage(pageNumber) {
  if (!pageNumber) return
  const params = { ...localFilters.value, page: pageNumber }
  router.get('/bulk-requests', params, {
    preserveState: true,
    replace: true,
    only: ['items']
  })
}

function goToUrl(url) {
  if (!url) return
  router.visit(url, { preserveState: true, preserveScroll: true, only: ['items'] })
}

let echoInstance = null
onMounted(() => {
  if (import.meta.env.VITE_PUSHER_APP_KEY) {
    import('laravel-echo')
      .then(({ default: Echo }) => {
        return import('pusher-js').then((PusherModule) => {
          window.Pusher = PusherModule.default
          echoInstance = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
            forceTLS: true,
        });

          echoInstance.channel('bulk-requests')
            .listen('.BulkRequestUpdated', (e) => {
              router.reload({ only: ['items'] })
            })
        })
      })
      .catch(() => {
        // ignore if Echo/Pusher not available in this environment
      })
  }
})

onBeforeUnmount(() => {
  if (echoInstance && echoInstance.disconnect) {
    echoInstance.disconnect()
  }
})


</script>

<style scoped>
table td, table th { vertical-align: middle; }
</style>
