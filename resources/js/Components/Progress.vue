<template>
  <div class="flex flex-wrap">
    <div>
      <span>Processed {{current}} of {{total}}</span>
    </div>
  </div>
</template>
  
<script>
export default {
  props: {
    total: {
      default: 0
    },
    current: {
      default: 0
    }
  },
}
</script> 