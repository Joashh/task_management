<template>
  <div class="flex flex-col justify-center w-full  items-center flex-1">
    <div class="self-center flex flex-col justify-center p-5 shadow-sm rounded-lg gap-3 w-100 ">
      <h1 class="font-bold font-sans text-xl text-center p-4">Login</h1>
      <div class="flex flex-col gap-2">
        <input
          v-model="formData.email"
          type="email"
          class="rounded-lg shadow-xs border border-gray-200 p-2 font-sans focus:outline-0"
          placeholder="Email"
        />
        <input
          v-model="formData.password"
          type="password"
          class="rounded-lg shadow-xs border border-gray-200 p-2 font-sans focus:outline-0"
          placeholder="Password"
        />
      </div>

      <div class="flex gap-2 justify-end">
        <button
          class="border border-gray-200 p-3 rounded-md shadow-md cursor-pointer hover:bg-gray-300 transition ease-in-out"
          @click="handleLogin"
        >
          Login
        </button>
        <button
          class="border border-gray-200 p-3 rounded-md shadow-md cursor-pointer hover:bg-gray-300 transition ease-in-out"
          @click="goRegister"
          type="button"
        >
          Create account
        </button>
      </div>

      <ul v-if="errors.length" class="px-4 py-2 bg-red-100 rounded-lg font-sans">
        <li v-for="(error, index) in errors" :key="index" class="my-2 text-red-500">
          {{ error }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useAuthStore } from '@/plugin/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const auth = useAuthStore()
const formData = reactive({
  email: '',
  password: '',
  device_name: 'browser'
})
const errors = ref([])

const handleLogin = async () => {
  errors.value = []
  try {
    await auth.login(formData)
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = Object.values(err.response.data.errors).flat()
    }
  }
}

const goRegister = () =>
{
  router.push({ name: 'Register' })
}
</script>

