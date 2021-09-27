<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ website.name }} URLs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 mb-6 flex justify-end">
                    <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="scanSiteUrls()">
                        Automaticly discover
                    </jet-button>

                    <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="addUrl()">
                        Manually add a url
                    </jet-button>
                </div>
                <template v-if="urls.length > 0">
                    <template v-for="url in urls" v-bind:key="url.id">
                        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                            <div class="flex flex-row">
                                <div class="flex-grow">
                                    <inertia-link :href="route('sites.show', {id: url.id})"><h2 class="text-2xl font-bold mb-2 text-gray-800">{{url.id}} - {{url.url}}</h2></inertia-link>
                                </div>
                                <div class="flex flex-col items-end">
                                    <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmUrlDeletion(url)">
                                        Delete
                                    </button>
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
    
    <!-- Scan for URLs Confirmation Modal -->
    <jet-confirmation-modal :show="showScanModal" @close="showScanModal = false">
        <template #title>
            Auto-scan
        </template>

        <template #content>
            This will remove all existing URL's and automatically scan your site for urls. Are you sure you want to continue?
        </template>

        <template #footer>
            <jet-secondary-button @click="showScanModal = false">
                No
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click="autoScan" :class="{ 'opacity-25': scanForm.processing }" :disabled="scanForm.processing">
                Yes
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>

    <!-- Delete URL Confirmation Modal -->
    <jet-confirmation-modal :show="urlBeingDeleted" @close="urlBeingDeleted = null">
        <template #title>
            Delete url?
        </template>

        <template #content>
            Are you sure you would like to delete this url and all associated data, this can not be reversed?
        </template>

        <template #footer>
            <jet-secondary-button @click="urlBeingDeleted = null">
                Cancel
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click="deleteUrl" :class="{ 'opacity-25': deleteUrlForm.processing }" :disabled="deleteUrlForm.processing">
                Delete
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>

    <!-- Delete Token Confirmation Modal -->
    <modal-form :show="addNewUrl" @close="addNewUrl = null">
        <template #title>
            Manually add a URL
        </template>

        <template #content>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="url" value="Url" />
                <jet-input id="url" type="text" class="mt-1 block w-full" v-model="addUrlForm.url" autofocus />
                <jet-input-error :message="addUrlForm.errors.name" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="closeAddUrl">
                Cancel
            </jet-secondary-button>
            <jet-button :class="{ 'opacity-25': addUrlForm.processing }" @click="createUrl" :disabled="addUrlForm.processing">
                Add
            </jet-button>
        </template>
    </modal-form>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import ModalForm from '@/Components/ModalForm'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        props: [
            'website',
            'urls'
        ],
        components: {
            AppLayout,
            JetButton,
            JetInput,
            JetInputError,
            JetLabel,
            ModalForm,
            JetConfirmationModal,
            JetSecondaryButton,
            JetDangerButton,
        },
        data() {
            return {
                deleteUrlForm: this.$inertia.form(),
                scanForm: this.$inertia.form(),
                urlBeingDeleted: null,
                showScanModal: false,
                addNewUrl: false,
                addUrlForm: this.$inertia.form({
                    url: '',
                    autoscan: false,
                }, {
                    resetOnSuccess: true,
                })
            }
        },
        methods: {
            confirmUrlDeletion(url) {
                this.urlBeingDeleted = url
            },

            closeAddUrl() {
                this.addNewUrl = false;
            },

            addUrl() {
                this.addNewUrl = true
            },

            scanSiteUrls() {
                this.showScanModal = true;
            },

            deleteUrl() {
                this.deleteUrlForm.delete(route('sites.urls.delete', {
                        id: this.website.id,
                        url_id: this.urlBeingDeleted.id, 
                }), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.urlBeingDeleted = null),
                })
            },

            autoScan() {
                this.deleteUrlForm.post(route('sites.urls.scan', {
                        id: this.website.id,
                }), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.showScanModal = false),
                })
            },

            createUrl() {
                this.addUrlForm.post(route('sites.urls.add', { 
                    id: this.website.id 
                }), {
                    errorBag: 'createUrl',
                    preserveScroll: true,
                    preserveState: false,
                    resetOnSuccess: true,
                    onSuccess: () => { this.closeAddUrl() }
                });
            },
        }
    }
</script>
