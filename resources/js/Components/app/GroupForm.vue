<script setup>

import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputTextarea from "@/Components/InputTextarea.vue";

defineProps({
    form: Object
})
</script>

<template>
    <div class="mb-3">
        <label>Group Name</label>
        <TextInput
            v-model="form.name"
            autofocus
            class="mt-1 block w-full"
            required
            type="text"
        />
    </div>

    <div class="mb-3">
        <label>
            <Checkbox v-model:checked="form.auto_approval" name="remember"/>
            Enable Auto Approval
        </label>
    </div>

    <div class="mb-3">
        <label>About Group</label>
        <InputTextarea v-model="form.about" class="w-full"/>
    </div>
</template>

<style scoped>

</style>
