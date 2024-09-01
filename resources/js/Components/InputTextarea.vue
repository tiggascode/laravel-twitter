<script setup>
import {onMounted, ref, watch} from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const emit = defineEmits(['update:modelValue'])

const input = ref(null);


const props = defineProps({
    modelValue: {
        type: String,
        required: false,
    },
    placeholder: String,
    autoResize: {
        type: Boolean,
        default: true,
    }
})

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

watch(() => props.modelValue, () => {
    adjustHeight()
})

defineExpose({focus: () => input.value.focus()});

function onInputChange($event) {
    emit('update:modelValue', $event.target.value)
    adjustHeight()
}

function adjustHeight() {
    if (props.autoResize) {
        input.value.style.height = 'auto'
        input.value.style.height = (input.value.scrollHeight + 1) + 'px'
    }
}

onMounted(() => {
    adjustHeight()
})
</script>

<template>
    <textarea
        ref="input"
        v-model="model"
        :placeholder="placeholder"
        :value="modelValue"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
         focus:border-indigo-500 dark:focus:border-indigo-600
         focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        @input="onInputChange"
    ></textarea>
</template>
