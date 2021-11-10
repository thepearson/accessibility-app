<template>
    <dashboard-layout>
        <template #header>
            <div class="">
                <h2 class="font-semibold text-gray-800 text-xl leading-tight">{{ website.name }} : {{ title }}</h2>
                <h3 class="text-gray-500 text-sm leading-10">{{ website.base_url }}</h3>
            </div>
        </template>

        <template #activeTask v-if="active_scan">
            <loader :task="'Scanning site'" :total="100" :message="'Message'" :current="33" />
        </template>

        <template #contextButtons>
            <div class="p-6 mb-6 flex justify-end">
                <jet-button :disabled="active_scan" class="cursor-pointer ml-6 text-sm text-white-500" @click="confirmSiteScan(website)">
                    Scan now
                    <div class="loader" v-show="active_scan">scanning...</div>
                </jet-button>
            </div>
        </template>

        <template #menu>
            <site-nav :current="website.id" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <template v-for="scan in scans" v-bind:key="scan.id">
                    <div class="bg-white p-6 rounded-lg shadow-lg mb-6 flex flex-col">
                        <div class="flex flex-row">
                            <div class="flex-grow">
                                <inertia-link :href="route('sites.show', {id: scan.id})"><h2 class="text-2xl font-bold mb-2 text-gray-800">{{scan.created_at}}</h2></inertia-link>
                                <p class="text-gray-700">{{scan.type}}</p>
                            </div>
                        </div>
                    </div>
                </template>
            
            </div>
        </div>

    </dashboard-layout>

        <!-- Delete Website Confirmation Modal -->
    <jet-confirmation-modal :show="siteBeingScanned" @close="siteBeingScanned = null">
        <template #title>
            Scan website?
        </template>

        <template #content>
            Are you sure you would like to scan this website?
        </template>

        <template #footer>
            <jet-secondary-button @click="siteBeingScanned = null">
                Cancel
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click="scanSite" :class="{ 'opacity-25': scanSiteForm.processing }" :disabled="scanSiteForm.processing">
                Scan
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>



<script>
    import DashboardLayout from '@/Layouts/DashboardLayout'
    import SiteNav from '@/Components/SiteNav'
    import Loader from '@/Components/Loader'
    import JetButton from '@/Jetstream/Button'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'

    export default {
        props: [
            'website',
            'latestScan',
            'urls',
            'scans',
            'active_scan'
        ],
        components: {
            Loader,
            JetButton,
            JetDangerButton,
            JetSecondaryButton,
            DashboardLayout,
            SiteNav,
            JetConfirmationModal
        },

        data() {
            return {
                title: "Scans",
                scanSiteForm: this.$inertia.form(),
                siteBeingScanned: null,
            }
        },

        methods: {
            confirmSiteScan(site) {
                this.siteBeingScanned = site
            },
            scanSite() {
                this.scanSiteForm.post(route('sites.scan', {
                        id: this.siteBeingScanned.id
                }), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.siteBeingScanned = null),
                })
            },
        }
    }
</script>
