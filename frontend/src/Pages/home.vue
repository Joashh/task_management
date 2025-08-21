<template>
  <div class="flex flex-col flex-1 gap-3 p-2 overflow-hidden">

    <!-- Top controls -->
    <div class="flex gap-2 w-full">
      <input v-model="searchTerm" @keyup.enter="openTopMatch" type="text"
        class="shadow-md border border-gray-200 rounded-md flex-1 p-2" placeholder="Search task..." />

      <!-- Only show Add Task button for admin -->
      <button v-if="auth.user?.role === 'admin'"
        class="shadow-md p-2 rounded-md border border-gray-200 cursor-pointer hover:bg-gray-300" @click="toggleDiv">
        Add Task
      </button>
    </div>


    <!-- Summary + Welcome -->
    <div class="flex flex-col gap-4">
      <Summary :task-count="tasks.length" :completed-count="completedTasksCount" :pending-count="pendingTasksCount" />
      <div class="flex justify-between items-center">
        <h1 class="font-bold text-xl">Welcome, {{ userName }}!</h1>
        <div class="flex gap-2 justify-center mb-4">
          <div>
            <Bars3BottomRightIcon class="h-10 w-7" />
          </div>

          <select v-model="filterStatus" class=" p-2 rounded ">

            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>

          <select v-model="filterPriority" class=" p-2 rounded">
            <option value="all">All Priority</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>

      </div>
    </div>

    <div class="fixed inset-0 bg-black/60 flex justify-center items-center z-50 h-screen p-5" v-if="Showdiv">
      <div class="bg-white shadow-md p-6 rounded-md flex flex-col w-full max-w-md gap-4">

        <!-- Task Title -->
        <div class="flex flex-col">
          <label class="font-semibold mb-1">Task Title</label>
          <input v-model="title" placeholder="Enter task title" class="border p-2 rounded w-full" />
        </div>

        <!-- Description -->
        <div class="flex flex-col">
          <label class="font-semibold mb-1">Description</label>
          <textarea v-model="description" placeholder="Enter description" class="border p-2 rounded w-full"></textarea>
        </div>

        <!-- Status -->
        <div class="flex flex-col">
          <label class="font-semibold mb-1">Status</label>
          <select v-model="selectedStatus" class="border p-2 rounded w-full text-black">
            <option v-for="s in statusOptions" :key="s" :value="s">
              {{ s }}
            </option>
          </select>
        </div>

        <!-- Assign Users -->
        <div class="flex flex-col">
          <label class="font-semibold mb-1">Assign Users</label>
          <select multiple v-model="selectedUsers" class="border p-2 rounded w-full text-black h-32">
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>

        <!-- Priority -->
        <div class="flex flex-col">
          <label class="font-semibold mb-1">Priority</label>
          <select v-model="priority" class="border p-2 rounded w-full">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 justify-end mt-2">
          <button @click="handleClick" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Task
          </button>
          <button @click="toggleDiv" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
            Cancel
          </button>
        </div>
      </div>
    </div>

    <div v-if="filterStatus !== 'all' || filterPriority !== 'all' || searchTerm"
      class="flex-1 flex flex-col gap-3 justify-center items-center mt-4 overflow-y-auto p-2">

      <div v-for="task in filteredTasks" :key="task.id"
        class="shadow-xs p-4 relative border border-gray-200 rounded-md w-full bg-gray-50">
        <div class="h-2 w-full rounded-t-md absolute top-0 left-0"
          :class="task.status === 'pending' ? 'bg-red-300' : 'bg-green-500'"></div>
        <div class="flex justify-between items-center mt-2">
          <div class="w-1/2">
            <h1 class="font-bold">{{ task.title }}</h1>
            <p class="w-full line-clamp-1">{{ task.description }}</p>
          </div>
          <h1 class="text-sm">{{ new Date(task.created_at).toLocaleString() }}</h1>
        </div>
      </div>
    </div>

    
    <!-- Task List -->
    <div v-if="filterStatus == 'all' && filterPriority == 'all'"
      class="flex-1 flex flex-col rounded-lg shadow-md border border-gray-200 overflow-hidden">
      <div class="flex-1 overflow-y-auto p-2 ">

        <draggable v-model="tasks" item-key="id" @end="updateOrder"
          class="flex flex-col gap-3 justify-center items-center">
          <template #item="{ element: task }">
            
            <div class="shadow-xs p-4 relative border border-gray-200 rounded-md w-full cursor-pointer"
              @click="openModal(task)">

              <div class="h-2 w-full rounded-t-md absolute top-0 left-0"
                :class="task.status === 'pending' ? 'bg-red-300' : 'bg-green-500'"></div>
              <div class="flex justify-between items-center mt-2 gap-4">
                <i class="pi pi-star-fill" :class="{
                  'text-green-500': task.priority === 'low',
                  'text-yellow-500': task.priority === 'medium',
                  'text-red-500': task.priority === 'high'
                }"></i>
                <div class="flex justify-between w-full">
                  <div class="w-1/2">
                    <h1 class="font-bold">{{ task.title }}</h1>
                    <p class="w-full line-clamp-1">{{ task.description }}</p>
                  </div>
                  <h1 class="text-sm">{{ new Date(task.created_at).toLocaleString() }}</h1>
                </div>

              </div>
            </div>
            
          </template>
        </draggable>

      </div>
     
    </div>
    <Modal v-if="showModal" :toggle-modal="toggleModal" :task="selectedTask" @update-task="updateTask" />

  </div>
</template>



<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import Summary from '@/components/summary.vue';
import { useAuthStore } from '@/plugin/auth';
import draggable from 'vuedraggable';
import Modal from '@/components/modal.vue';
import { Bars3BottomRightIcon } from '@heroicons/vue/24/solid';

const auth = useAuthStore()

const Showdiv = ref(false)
const toggleDiv = () => Showdiv.value = !Showdiv.value

const showModal = ref(false)
const toggleModal = () => showModal.value = !showModal.value

const title = ref('')
const description = ref('')
const statusOptions = ref(['pending', 'completed'])
const selectedStatus = ref('pending')
const priority = ref('low')
const selectedUsers = ref([])
const users = ref([])
const tasks = ref([])
const searchTerm = ref('');

const selectedTask = ref(null)

const filterStatus = ref('all'); // all, pending, completed
const filterPriority = ref('all'); // all, low, medium, high



// Axios instance with cookies
const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true, // important for sending session cookie
})

const updateOrder = async () => {
  try {
    // Step 1: Fetch CSRF cookie
    await api.get('/sanctum/csrf-cookie');

    // Step 2: Read the token from the cookie
    const getCookie = (name) => {
      const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
      return match ? decodeURIComponent(match[2]) : null;
    };
    const xsrfToken = getCookie('XSRF-TOKEN');

    // Step 3: Prepare order payload
    const orderPayload = tasks.value.map((task, index) => ({
      id: task.id,
      order: index + 1,
    }));

    // Step 4: Send POST request with token
    await api.post('/api/update-task-order', { tasks: orderPayload }, {
      headers: { 'X-XSRF-TOKEN': xsrfToken }
    });

    console.log('Order updated');
  } catch (err) {
    console.error('Failed to update order', err);
  }
};






// Fetch users and tasks
onMounted(async () => {
  try {
    if (!auth.user) {
      await auth.fetchUser()
    }
    // Step 0: Initialize CSRF & session
    await api.get('/sanctum/csrf-cookie')

    // Step 1: Fetch users
    const resUsers = await api.get('/api/users')
    users.value = resUsers.data

    // Step 2: Fetch tasks for current user
    const resTasks = await api.get('/api/my-tasks')
    tasks.value = resTasks.data
  } catch (err) {
    console.error(err.response || err)
  }
})
const userName = computed(() => auth.user?.name || 'Guest')

// Create task

const getCookie = (name) => {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
};

const createTask = async () => {
  if (!title.value || selectedUsers.value.length === 0) return;

  try {
    // Step 1: Get CSRF cookie
    await api.get('/sanctum/csrf-cookie');

    // Step 2: Read the XSRF token from cookie
    const xsrfToken = getCookie('XSRF-TOKEN');

    // Step 3: Send POST request with token in headers
    const res = await api.post(
      '/api/recordtask',
      {
        title: title.value,
        description: description.value,
        status: selectedStatus.value,
        priority: priority.value,
        user_ids: selectedUsers.value,
      },
      {
        headers: { 'X-XSRF-TOKEN': xsrfToken },
      }
    );

    console.log(res.data);
    tasks.value.push(...(res.data.tasks || []));

    // Reset form
    title.value = '';
    description.value = '';
    selectedStatus.value = 'pending';
    priority.value = 'low';
    selectedUsers.value = [];

    alert('Task assigned!');
    Showdiv.value = false;
  } catch (err) {
    console.error(err.response || err);
    alert('Failed to assign task. Check console.');
  }
};
const completedTasksCount = computed(() =>
  tasks.value.filter(task => task.status === 'completed').length
);
const pendingTasksCount = computed(() =>
  tasks.value.filter(task => task.status === 'pending').length
);

const openModal = (task) => {
  selectedTask.value = task
  showModal.value = true
}

const openTopMatch = () => {
  const match = tasks.value.find(task =>
    task.title.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    task.description.toLowerCase().includes(searchTerm.value.toLowerCase())
  );

  if (match) {
    selectedTask.value = match; // set the task for modal
    showModal.value = true;     // open modal
  }
};




const updateTask = (updatedTask) => {
  const index = tasks.value.findIndex(t => t.id === updatedTask.id)
  if (index !== -1) {
    tasks.value[index] = updatedTask
  }
}

const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    const statusMatch =
      filterStatus.value === 'all' || task.status === filterStatus.value;
    const priorityMatch =
      filterPriority.value === 'all' || task.priority === filterPriority.value;
    const searchMatch =
      !searchTerm.value ||
      task.title.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      task.description.toLowerCase().includes(searchTerm.value.toLowerCase());
    return statusMatch && priorityMatch && searchMatch;
  });
});



// Handle button click
const handleClick = () => {
  createTask()
  toggleDiv()
}
</script>
