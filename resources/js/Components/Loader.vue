<template>
  <div class="relative pt-1 pb-3">
    <div class="flex mb-2 items-center justify-between">
      <div>
        <span
          class="
            text-xs
            font-semibold
            inline-block
            py-1
            px-2
            uppercase
            rounded-full
            text-blue-600
            bg-blue-200
          "
        >
          {{ task }}
        </span>
        <span class="pl-4 text-xs text-gray-500">{{ message }}</span>
      </div>
      <div class="text-right">
        <span class="text-xs font-semibold inline-block text-blue-600">
          {{percentage}}%
        </span>
      </div>
    </div>
    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
      <div
        :style="{ width: percentage + '%' }"
        class="
          shadow-none
          flex flex-col
          text-center
          whitespace-nowrap
          text-white
          justify-center
          bg-blue-500
        "
      ></div>
    </div>
  </div>
</template>
  
<script>
import JetNavLink from '@/Jetstream/NavLink'

export default {
  props: [
    'task',
    'current',
    'total',
    'message'
  ],
  components: {

  },
  computed: {
    percentage() {
      if (this.total == 0) {
        return 1;
      }
      return Math.round((100/this.total)*this.current);
    }
  }
};
</script> 