<template>
    
    <div
    class="fixed inset-0 bg-black/60 flex justify-center items-center z-50 p-5 overflow-auto">
        <div class="bg-white rounded-lg shadow-md p-6 w-full max-w-md flex flex-col  relative">
            <XMarkIcon class="h-6 w-6 cursor-pointer absolute top-4 right-4" @click="props.toggleModal" />
            <div class="flex gap-2">
                <h1 class="font-bold text-xl">{{ task.title }}</h1>
                <div class="inline-block px-3 py-1 rounded-full font-semibold text-sm" :class="{
                    'bg-green-200 text-green-800': task.status === 'completed',
                    'bg-yellow-300 text-yellow-900': task.status === 'pending'
                }">
                    {{ task.status }}
                </div>
                <div class="inline-block px-3 py-1 rounded-full font-semibold text-sm" :class="{
                    'bg-red-500 text-green-800': task.priority === 'high',
                    'bg-orange-400 text-yellow-900': task.priority === 'medium',
                    'bg-green-400 text-yellow-900': task.priority === 'low'
                }">
                    {{ task.priority }}
                </div>
            </div>
            <p class="text-gray-700 text-justify mt-4">{{ task.description }}</p>
            <div class="flex gap-3 justify-end mt-4">
                <button @click="markAsDone" class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-600">
                    Mark as Done
                </button>
                <button class="px-4 py-2 bg-gray-400 text-white rounded-md shadow hover:bg-gray-500" @click="props.toggleModal">
                    Go Back
                </button>
         </div>
        </div>    
    </div>
    
</template>

<script setup>
import { XMarkIcon } from '@heroicons/vue/24/solid';
import axios from 'axios';
import { motion } from 'motion-v';

const emit = defineEmits(['update-task'])

const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true // important for Sanctum
})

const getCookie = (name) => {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

const markAsDone = async () => {
  try {
    // Step 1: Fetch CSRF cookie
    await api.get('/sanctum/csrf-cookie');

    // Step 2: Read token from cookie
    const xsrfToken = getCookie('XSRF-TOKEN');

    
    await api.put(`/api/tasks/${props.task.id}`, 
      { status: 'completed' },
      { headers: { 'X-XSRF-TOKEN': xsrfToken } }
    );

   
    emit('update-task', { ...props.task, status: 'completed' });
    props.toggleModal();
  } catch (err) {
    console.error(err);
    alert('Failed to update task. CSRF might be missing.');
  }
}
const props = defineProps({
    toggleModal: {
        type: Function,
        required: true
    },
    task: {
        type: Object,
        required: true                                                                                      
    }
})
</script>
