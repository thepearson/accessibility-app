<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ website.name }}
            </h2>
        </template>

        <div class="py-12 pt-1">

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout';
    import Tabs from '@/Components/Tabs';

    export default {
        props: [
            'website',
            'latestScan',
            'urls',
        ],
        components: {
            AppLayout,
            Tabs,
        },

        data() {
            return {
                tabs: [
                    {
                        label: "Violations",
                        active: true,
                        url: `/sites/${this.website.id}/violations`,
                    },
                    {
                        label: "Urls",
                        
                        url: `/sites/${this.website.id}/urls`,
                    },
                ]
            }
        },

        methods: {
            
        }
    }
</script>
