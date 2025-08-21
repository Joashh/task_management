<template>
  <div class="min-h-screen max-h-screen  flex flex-col ">
    
    <header class="bg-gray-100 text-black p-4 shadow-md">
      <div class="container mx-auto flex justify-between items-center">
        
        <div class="flex items-center gap-2 ">
    <img src="/muscle.png" class="h-10 w-10" alt="">
    <h1 class="text-xl font-bold "> <span class="text-blue-500">Chore</span>Champ</h1>
  </div>
        
        
       <div class="relative md:hidden">
    <!-- Burger icon -->
    <button @click="menuOpen = !menuOpen" class="p-2 rounded hover:bg-gray-200">
      <Bars3Icon class="h-9 w-9" />
    </button>

   
    <div
      v-if="menuOpen"
      class=" absolute right-0 mt-2 bg-white shadow-lg rounded-lg p-4 flex flex-col min-w-[150px] z-50"
    >
      <ul class="flex flex-col space-y-2">
        <li><a href="/Home" class="hover:underline">Home</a></li>
        <li><a href="/about" class="hover:underline">About</a></li>
        <li v-if="!auth.isAuthenticated">
          <a href="/login" class="hover:underline">Login</a>
        </li>
        <li v-else>
          <button @click="auth.logout" class="hover:underline cursor-pointer">Logout</button>
        </li>
      </ul>
    </div>
  </div>
        <nav class="hidden md:block">
          <ul class="flex space-x-4">
            <li><a href="/Home" class="hover-underline">Home</a></li>
            <li><a href="/about" class="hover-underline">About</a></li>
            <li v-if="!auth.isAuthenticated"><a  href="/login" class="hover:underline">Login</a></li>
             <li v-else><button  @click="auth.logout" class="hover-underline cursor-pointer">Logout</button></li>
          </ul>
        </nav>
      </div>
    </header>

    
    <main class="flex-1 p-4 flex flex-col overflow-hidden">
      <slot></slot>
    </main>

   
    <footer class="bg-gray-200 text-gray-700 p-4 text-center ">
      &copy; 2025 ChoreChamp - Your task organizer and scheduler. Manage your task easily.
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Bars3Icon } from '@heroicons/vue/24/solid'
import { useAuthStore } from '@/plugin/auth';

const auth = useAuthStore()
const menuOpen = ref(false)
</script>
