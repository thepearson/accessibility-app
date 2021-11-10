<template>
    <dashboard-layout>
        <template #header>
            <div class="w-1/2">
                <h2 class="font-semibold text-gray-800 text-xl leading-tight">{{ website.name }} : {{ title }}</h2>
                <h3 class="text-gray-500 text-sm leading-10">{{ website.base_url }}</h3>
            </div>
        </template>

        <template #menu>
            <site-nav :current="website.id" />
        </template>

        <template #activeTask v-if="active_crawl">
            <loader :task="'Crawling site'" :total="active_crawl.total" :message="loaderMessage" :current="active_crawl.complete" />
        </template>

        <template #contextButtons>
            <div class="p-6 mb-6 flex justify-end">
                <jet-button :disabled="active_crawl" class="cursor-pointer ml-6 text-sm text-white-500" @click="scanSiteUrls()">
                    Automaticly discover
                    <div class="loader" v-show="active_crawl">working...</div>
                </jet-button>

                <jet-button class="cursor-pointer ml-6 text-sm text-white-500" @click="addUrl()">
                    Manually add a url
                </jet-button>
            </div>
        </template>

        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <template v-if="urls.total > 0">
                    <span>Showing {{urls.data.length}} of {{urls.total}} urls</span>
                    
                    <template v-for="url in urls.data" v-bind:key="url.id">
                        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                            <div class="flex flex-row">
                                <div class="flex-grow">
                                    <inertia-link :href="route('sites.show', {id: url.id})"><h2 class="text-sm font-bold mb-2 text-gray-800">{{url.url}}</h2></inertia-link>
                                </div>
                                <div class="flex flex-col items-end">
                                    <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmUrlDeletion(url)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <pagination class="mt-6" :links="urls.links" />
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
    </dashboard-layout>    
    
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
    import DashboardLayout from '@/Layouts/DashboardLayout'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import ModalForm from '@/Components/ModalForm'
    import Loader from '@/Components/Loader'
    import Pagination from '@/Components/Pagination'
    import JetInput from '@/Jetstream/Input'
    import SiteNav from '@/Components/SiteNav'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        props: [
            'website',
            'urls',
            'active_crawl',
            'latest_url'
        ],
        components: {
            DashboardLayout,
            JetButton,
            Loader,
            SiteNav,
            JetInput,
            JetInputError,
            JetLabel,
            Pagination,
            ModalForm,
            JetConfirmationModal,
            JetSecondaryButton,
            JetDangerButton,
        },
        computed: {
            loaderMessage() {
                if (this.latest_url) {
                    return `Scanned ${this.active_crawl.complete} of ${this.active_crawl.total} - Last added ${this.latest_url.url}`;
                }
                else {
                    return null;
                }
            }
        },
        mounted() {
            if (this.active_crawl) {
                this.interval = this.startPoll();  
            }
        },

        unmounted() {
            this.endPoll();
        },

        data() {
            return {
                title: "URLs",
                interval: null,
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

            updateData() {
                this.$inertia.reload({ 
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: page => {
                        if (!page.props.active_crawl) {
                            this.endPoll();
                        }
                    }
                });
            },
            startPoll() {
                return setInterval(function() {
                    this.updateData(); 
                }.bind(this), 5000)
            },
            endPoll() {
                clearInterval(this.interval);
            },
            confirmUrlDeletion(url) {
                this.urlBeingDeleted = url;
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
                this.deleteUrlForm.post(route('sites.urls.crawl', {
                        id: this.website.id,
                }), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.showScanModal = null;
                        this.interval = this.startPoll();
                    },
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
