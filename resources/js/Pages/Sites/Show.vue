<template>
    <dashboard-layout>
        <template #header>
            <div class="">
                <h2 class="font-semibold text-gray-800 text-xl leading-tight">{{ website.name }} : {{ title }}</h2>
                <h3 class="text-gray-500 text-sm leading-10">{{ website.base_url }}</h3>
            </div>
        </template>

        <div class="py-12 pt-1">
            <h2>{{ title }}</h2>
        </div>

        <template #menu>
            <site-nav :current="website.id" />
        </template>
    </dashboard-layout>
</template>

<script>
    import DashboardLayout from '@/Layouts/DashboardLayout';
    import SiteNav from '@/Components/SiteNav';

    export default {
        props: [
            'website',
            'latestScan',
            'urls',
            'title',
        ],
        components: {
            DashboardLayout,
            SiteNav
        },

        data() {
            return {

            }
        },

        methods: {
            
        }
    }
</script>
