<template>
  <main class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow-sm">
    <h1 class="text-2xl font-semibold mb-4">Ticket Escalation</h1>

    <div class="space-y-3 text-sm text-slate-700">
      <div><strong>Ticket ID:</strong> {{ ticket.id }}</div>
      <div><strong>Subject:</strong> {{ ticket.subject }}</div>
      <div><strong>Priority:</strong> {{ ticket.priority }}</div>
      <div><strong>Status:</strong> {{ ticket.status }}</div>
      <div><strong>Escalation Date:</strong> {{ escalationDate }}</div>
    </div>

    <section class="mt-6">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" v-model="selectedChannels" value="email" />
        Email
      </label>
      <label class="inline-flex items-center gap-2 ml-4">
        <input type="checkbox" v-model="selectedChannels" value="slack" />
        Slack
      </label>
    </section>

    <button
      class="mt-6 px-5 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700 disabled:opacity-50"
      :disabled="isProcessing"
      @click="escalate"
    >
      {{ isProcessing ? 'Escalating...' : 'Escalate Ticket' }}
    </button>

    <div v-if="message" class="mt-4 p-4 rounded border" :class="messageClass">{{ message }}</div>
  </main>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const ticket = ref(JSON.parse(document.getElementById('app').dataset.ticket));
const isProcessing = ref(false);
const message = ref('');
const selectedChannels = ref(['email', 'slack']);

const escalationDate = computed(() => ticket.value.escalated_at ? new Date(ticket.value.escalated_at).toLocaleString() : 'Not escalated yet');
const messageClass = computed(() => ({ 'bg-green-100 border-green-300 text-green-900': message.value && !message.value.includes('failed'), 'bg-amber-100 border-amber-300 text-amber-900': message.value.includes('failed') }));

async function escalate() {
  isProcessing.value = true;
  message.value = '';

  try {
    const response = await axios.post(`/api/tickets/${ticket.value.id}/escalate`, {
      channels: selectedChannels.value,
    });

    ticket.value = response.data.ticket;
    message.value = 'Ticket escalated and notifications queued.';
  } catch (error) {
    message.value = error.response?.data?.message || 'Escalation failed. Please try again.';
  } finally {
    isProcessing.value = false;
  }
}
</script>
