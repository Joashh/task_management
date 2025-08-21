<template>
  <div class="flex flex-col justify-center flex-1">
    <form @submit.prevent="registerUser" class="self-center flex flex-col p-5 shadow-sm rounded-lg gap-3 w-100">
      <h1 class="font-bold text-xl text-center p-4">Register</h1>
      
      <div class="flex flex-col gap-2">
        <input v-model="formData.name" type="text"
               class="rounded-lg shadow-xs border border-gray-200 p-2 focus:outline-0"
               placeholder="Name">
               
        <input v-model="formData.email" type="email"
               class="rounded-lg shadow-xs border border-gray-200 p-2 focus:outline-0"
               placeholder="Email">
               
        <input v-model="formData.password" type="password"
               class="rounded-lg shadow-xs border border-gray-200 p-2 focus:outline-0"
               placeholder="Password">
               
        <input v-model="formData.password_confirmation" type="password"
               class="rounded-lg shadow-xs border border-gray-200 p-2 focus:outline-0"
               placeholder="Confirm Password">
               
        <select v-model="formData.role" class="rounded-lg shadow-xs border border-gray-200 p-2 font-sans focus:outline-0">
          <option class="text-sm" value="" disabled>Select a role</option>
          <option class="text-sm" value="user">User</option>
          <option class="text-sm" value="admin">Admin</option>
        </select>
      </div>

      <div class="flex gap-2 justify-end">
        <button type="button" @click="router.back()"
                class="border border-gray-200 p-3 rounded-md shadow-md cursor-pointer hover:bg-gray-300 transition ease-in-out">
          Back
        </button>
        <button type="submit"
                class="border border-gray-200 p-3 rounded-md shadow-md cursor-pointer hover:bg-gray-300 transition ease-in-out">
          Create account
        </button>
      </div>

      <ul v-if="Object.keys(errors).length" class="px-4 py-2 bg-red-100 rounded-lg">
        <li v-for="(messages, field) in errors" :key="field" class="my-2 text-red-500">
          <span v-for="(msg, index) in messages" :key="index">{{ msg }}</span>
        </li>
      </ul>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const formData = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: ''
})

const errors = reactive({})

const registerUser = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/register', formData)
    console.log(response.data)

    // Reset form
    Object.keys(formData).forEach(key => formData[key] = '')
    Object.keys(errors).forEach(key => delete errors[key])

    router.push('/login')
    $toaster.success('Account created successfully, now you can login!')
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      Object.assign(errors, err.response.data.errors)
      console.log(err.response.data.errors)
    }
  }
}
</script>
