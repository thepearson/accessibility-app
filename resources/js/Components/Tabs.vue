<template>
  <!-- Tabs -->
  <ul id="tabs" class="inline-flex pt-2 px-1 w-full border-b">
    <template v-for="(tab, k) in tabs" :key="k">
      <li v-if="tab.active" class="bg-white px-4 text-gray-800 font-semibold py-2 rounded-t border-t border-r border-l -mb-px">
        <div v-html="tab.label" />
      </li>
      <li v-else class="px-4 text-gray-800 font-semibold py-2 rounded-t">
        <inertia-link :href="tab.url" v-html="tab.label" />
      </li>
    </template>
  </ul>
</template>
  
<script>
export default {
  props: {
    tabs: Array,
  },
}
</script> 