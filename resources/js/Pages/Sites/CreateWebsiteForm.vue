<template>
    <jet-form-section @submitted="createWebsite">
        <template #title>
            Website Details
        </template>

        <template #description>
            Create a new website within your team profile
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Site name" />
                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="base_url" value="Site base URL" />
                <jet-input id="base_url" type="text" class="mt-1 block w-full" v-model="form.base_url" autofocus />
                <jet-input-error :message="form.errors.base_url" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Add
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    base_url: '',
                })
            }
        },

        methods: {
            createWebsite() {
                this.form.post(route('sites.add'), {
                    errorBag: 'createWebsite',
                    preserveScroll: true
                });
            },
        },
    }
</script>
