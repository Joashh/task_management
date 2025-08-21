<template>
 <div class="shadow-md border border-gray-200 rounded-md flex w-full">
  <div class="flex flex-1 divide-x divide-gray-200 text-center w-full">
    <div class="flex-1 p-3 min-w-[60px]">
      <span class="font-medium truncate block" title="Assigned">Assigned: </span> 
      {{ taskCount }}
    </div>
    <div class="flex-1 p-3 min-w-[60px]">
      <span class="font-medium truncate block" title="Completed">Completed: </span> 
      {{ completedCount }}
    </div>
    <div class="flex-1 p-3 min-w-[60px]">
      <span class="font-medium truncate block" title="Pending">Pending: </span> 
      {{ pendingCount }}
    </div>
    <div class="flex-1 p-3 min-w-[60px]">
      <span class="font-medium truncate block" title="Missed">Missed: </span> 0
    </div>
  </div>
</div>


</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  taskCount: {
    type: Number,
  },

   completedCount: {
    type: Number,
  
  },

   pendingCount: {
    type: Number,
    
  }


});
</script>