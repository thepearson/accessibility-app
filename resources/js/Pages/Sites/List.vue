<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Websites
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="table w-full">
                    <div class="table-row-group">
                        <template v-for="website in websites" v-bind:key="website.id">
                            <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                                <h2 class="text-2xl font-bold mb-2 text-gray-800">{{website.name}}</h2>
                                <p class="text-gray-700">{{website.base_url}}</p>
                                <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmSiteDeletion(website)">
                                    Delete
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            
            </div>
        </div>
    </app-layout>
    
    <!-- Delete Token Confirmation Modal -->
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
    </jet-confirmation-modal>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import CreateWebsiteForm from './CreateWebsiteForm';

    export default {
        props: [
            'websites',
        ],
        components: {
            AppLayout,
            JetConfirmationModal,
            JetSecondaryButton,
            JetDangerButton,
            CreateWebsiteForm,
        },
        data() {
            return {
                deleteSiteForm: this.$inertia.form(),
                siteBeingDeleted: null,
            }
        },
        methods: {
            confirmSiteDeletion(site) {
                this.siteBeingDeleted = site
            },

            deleteSite() {
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
