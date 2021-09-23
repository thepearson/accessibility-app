<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Website URLs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 mb-6 flex justify-end">
                    <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="scanSiteUrls(true)">
                        Automaticly discover
                    </jet-button>

                    <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="addSite()">
                        Manually add a url
                    </jet-button>
                </div>
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
                    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                        <div>
                            <h2>There's nothing here</h2>
                            <h3>Scan your site for urls.</h3>
                        </div>
                    </div>
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
