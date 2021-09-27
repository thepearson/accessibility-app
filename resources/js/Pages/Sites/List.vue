<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Websites
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 mb-6 flex justify-end">
                    <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="addSite()">
                        Add new site
                    </jet-button>
                </div>
                <template v-for="website in websites" v-bind:key="website.id">
                    <div class="bg-white p-6 rounded-lg shadow-lg mb-6 flex flex-col">
                        <div class="flex flex-row">
                            <div class="flex-grow">
                                <inertia-link :href="route('sites.show', {id: website.id})"><h2 class="text-2xl font-bold mb-2 text-gray-800">{{website.name}}</h2></inertia-link>
                                <p class="text-gray-700">{{website.base_url}}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <inertia-link class="cursor-pointer ml-6 text-sm" :href="route('sites.urls.list', { id: website.id })">
                                    Urls
                                </inertia-link>
                                <!-- <inertia-link class="cursor-pointer ml-6 text-sm" :href="route('sites.settings')">
                                    Settings
                                </inertia-link> -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmSiteDeletion(website)">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="">

                        </div>
                    </div>
                </template>
            
            </div>
        </div>
    </app-layout>
    
    <!-- Delete Website Confirmation Modal -->
    <jet-confirmation-modal :show="siteBeingDeleted" @close="siteBeingDeleted = null">
        <template #title>
            Delete website?
        </template>

        <template #content>
            Are you sure you would like to delete this website and all associated data, this can not be reversed?
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

    <!-- Delete Token Confirmation Modal -->
    <modal-form :show="addNewSite" @close="addNewSite = null">
        <template #title>
            Add a new Website
        </template>

        <template #content>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Site name" />
                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="addSiteForm.name" autofocus />
                <jet-input-error :message="addSiteForm.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="base_url" value="Site base URL" />
                <jet-input id="base_url" type="text" class="mt-1 block w-full" v-model="addSiteForm.base_url" autofocus />
                <jet-input-error :message="addSiteForm.errors.base_url" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <label class="flex items-center">
                    <jet-checkbox name="autoscan" v-model:checked="addSiteForm.autoscan" />
                    <span class="ml-2 text-sm text-gray-600">Automatically scan for urls?</span>
                </label>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="closeAddSite">
                Cancel
            </jet-secondary-button>
            <jet-button :class="{ 'opacity-25': addSiteForm.processing }" @click="createWebsite" :disabled="addSiteForm.processing">
                Add
            </jet-button>
        </template>
    </modal-form>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import ModalForm from '@/Components/ModalForm'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from '@/Jetstream/Button'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'

    export default {
        props: [
            'websites',
        ],
        emits: ['submitted'],
        components: {
            AppLayout,
            JetConfirmationModal,
            JetSecondaryButton,
            JetDangerButton,
            JetInput,
            JetInputError,
            JetLabel,
            JetButton,
            JetCheckbox,
            ModalForm,
        },
        data() {
            return {
                deleteSiteForm: this.$inertia.form(),
                siteBeingDeleted: null,
                addNewSite: null,
                addSiteForm: this.$inertia.form({
                    name: '',
                    base_url: '',
                    autoscan: false,
                }, {
                    resetOnSuccess: true,
                })
            }
        },
        methods: {
            confirmSiteDeletion(site) {
                this.siteBeingDeleted = site
            },

            closeAddSite() {
                this.addNewSite = false;
            },

            addSite() {
                this.addNewSite = true
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
            createWebsite() {
                this.addSiteForm.post(route('sites.add'), {
                    errorBag: 'createWebsite',
                    preserveScroll: true,
                    onSuccess: () => { this.addNewSite = false }
                });
            },
        }
    }
</script>
