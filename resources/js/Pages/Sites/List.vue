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
                            </div>
                        </template>
                    </div>
                </div>
            
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import CreateWebsiteForm from './CreateWebsiteForm';

    export default {
        props: [
            'websites',
        ],
        components: {
            AppLayout,
            CreateWebsiteForm,
        },
    }
</script>
