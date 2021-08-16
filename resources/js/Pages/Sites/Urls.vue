<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Website URLs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <template v-if="urls.length > 0">
                    <template v-for="url in urls" v-bind:key="url.id">
                        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                            <div class="flex flex-row">
                                <div class="flex-grow">
                                    <inertia-link :href="route('sites.show', {id: url.id})"><h2 class="text-2xl font-bold mb-2 text-gray-800">{{url.url}}</h2></inertia-link>
                                </div>
                                <div class="">
                                    x
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <h2>There aren't any URLs yet</h2>
                    <h3>Scan for site URLs?</h3>

                    <jet-button class="ml-2" @click="scanSiteUrls(true)">
                        Automatic
                    </jet-button>

                    <jet-secondary-button @click="scanSiteUrls(false)">
                        Interactive
                    </jet-secondary-button>
                </template>
            </div>
        </div>
    </app-layout>
    
    <!-- Delete Token Confirmation Modal
    <jet-confirmation-modal :show="siteBeingDeleted" @close="siteBeingDeleted = null">
        <template #title>
            Delete API Token
        </template>

        <template #content>
            Are you sure you would like to delete this API token?
        </template>

        <template #footer>
            <jet-secondary-button @click="siteBeingDeleted = null">
                Cancel
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click="deleteSite" :class="{ 'opacity-25': deleteSiteForm.processing }" :disabled="deleteSiteForm.processing">
                Delete
            </jet-danger-button>
        </template>
    </jet-confirmation-modal> -->
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'

    export default {
        props: [
            'website',
            'urls',
        ],
        components: {
            AppLayout,
            JetButton,
            JetConfirmationModal,
            JetSecondaryButton,
            JetDangerButton,
        },
        data() {
            return {
                deleteSiteForm: this.$inertia.form(),
                siteBeingScanned: false,
            }
        },
        methods: {
            confirmSiteDeletion(site) {
                this.siteBeingScanned = site
            },

            scanSiteUrls() {
                this.deleteSiteForm.delete(route('sites.delete', {
                        id: this.siteBeingDeleted.id
                }), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.siteBeingDeleted = null),
                })
            },
        }
    }
</script>
