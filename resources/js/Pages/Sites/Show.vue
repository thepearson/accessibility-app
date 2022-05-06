<template>
    <dashboard-layout>
        <template #header>
            <div class="">
                <h2 class="font-semibold text-gray-800 text-xl leading-tight">{{ website.name }} : {{ title }}</h2>
                <h3 class="text-gray-500 text-sm leading-10">{{ website.base_url }}</h3>
            </div>
        </template>

        <div class="m-6 py-12 pt-1">
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <stat-card title="Scanned pages" :stat=prettyScannedPages ></stat-card>
                <stat-card title="Average page size" :stat=prettySize ></stat-card>
                <stat-card title="Average load duration" :stat=prettyDuration></stat-card>
                <stat-card title="Accessibility violations" :stat=prettyViolations ></stat-card>
            </div>
        </div>
        <template #menu>
            <site-nav :current="website.id" />
        </template>
    </dashboard-layout>
</template>

<script>
    import DashboardLayout from '@/Layouts/DashboardLayout';
    import SiteNav from '@/Components/SiteNav';
    import StatCard from "@/Components/StatCard";
    import PrettyBytes from 'pretty-bytes';

    export default {
        props: [
            'website',
            'latestScan',
            'urls',
            'data',
            'title',
        ],
        components: {
            DashboardLayout,
            SiteNav,
            StatCard
        },

        computed: {
            prettySize() {
                if (this.data.stats.average_page_size && this.data.stats.average_page_size.hasOwnProperty('avg_size')) {
                    return PrettyBytes(parseInt(this.data.stats.average_page_size.avg_size));
                }
                return 'N/A';
            },
            prettyDuration() {
                if (this.data.stats.average_page_duration && this.data.stats.average_page_duration.hasOwnProperty('avg_duration')) {
                    return this.data.stats.average_page_duration.avg_duration/1000 + 's';
                }
                return 'N/A';
            },
            prettyScannedPages() {
                if (this.data.stats.scanned_pages && this.data.stats.scanned_pages.hasOwnProperty('pages')) {
                    return this.data.stats.scanned_pages.pages + ' pages.';
                }
                return 'N/A';
            },
            prettyViolations() {
                if (this.data.stats.acc_violations && this.data.stats.acc_violations.hasOwnProperty('violations')) {
                    return this.data.stats.acc_violations.violations + ' violations.';
                }
                return 'N/A';
            }

        },

        data() {
            return {

            }
        },

        methods: {
            
        }
    }
</script>
