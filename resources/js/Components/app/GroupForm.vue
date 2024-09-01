<script setup>

import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

const editor = ClassicEditor;
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|',
        'blockQuote'],
}
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
        <ckeditor v-model="form.about" :config="editorConfig" :editor="editor"></ckeditor>

    </div>
</template>

<style scoped>

</style>
