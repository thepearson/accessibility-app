<template>
    <form @submit.prevent="$emit('submitted')">
        <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
            <div class="px-6 py-4">
                <div class="text-lg">
                    <slot name="title">
                    </slot>
                </div>

                <div class="mt-4">
                    <slot name="content">
                    </slot>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-100 text-right">
                <slot name="footer">
                </slot>
            </div>
        </modal>
    </form>
</template>

<script>
    import Modal from '@/Jetstream/Modal'

    export default {
        emits: ['submitted', 'close'],

        components: {
            Modal,
        },

        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
        },

        computed: {
            hasActions() {
                return !! this.$slots.actions
            },
            close() {
                this.$emit('close')
            },
        }
    }
</script>
