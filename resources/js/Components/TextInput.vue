<script setup>
import {onMounted, ref} from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({focus: () => input.value.focus()});

defineProps({
    placeholder: String
})
</script>

<template>
    <input
        ref="input"
        v-model="model"
        :placeholder="placeholder"
        class="border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300
         focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500
          dark:focus:ring-indigo-600 rounded-md shadow-sm"
    />
</template>
