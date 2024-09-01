<script setup>

import InputTextarea from "@/Components/InputTextarea.vue";
import {ChatBubbleLeftEllipsisIcon, HandThumbUpIcon} from "@heroicons/vue/20/solid/index.js";
import IndigoButton from "@/Components/IndigoButton.vue";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import EditDeleteDropdown from "@/Components/app/EditDeleteDropdown.vue";
import {Link, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import instance from "@/axiosClient.js";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

const authUser = usePage().props.auth.user
const editingComment = ref(null)

const newCommentText = ref('')

const props = defineProps({
    post: Object,
    data: Object,
    parentComment: {
        type: [Object, null],
        default: null
    }
})

const emit = defineEmits(['commentCreate', 'commentDelete'])

function createComment() {
    instance.post(route('post.comment.create', props.post), {
        comment: newCommentText.value,
        parent_id: props.parentComment?.id || null,
    })
        .then(({data}) => {
            newCommentText.value = ''
            props.data.comments.unshift(data)
            if (props.parentComment) {
                props.parentComment.num_of_replies++
            }
            props.post.num_of_comments++
            emit('commentCreate', data)
        })
}

function deleteComment(comment) {
    if (!window.confirm('Are you sure you want to delete this comment?')) {
        return false;
    }
    instance.delete(route('comment.delete', comment.id))
        .then(({data}) => {
            const commentIndex = props.data.comments.findIndex(c => c.id === comment.id)
            props.data.comments.splice(commentIndex, 1)
            if (props.parentComment) {
                props.parentComment.num_of_replies--
            }
            props.post.num_of_comments--
            emit('commentDelete', comment)
        })
}

function updateComment() {
    instance.put(route('comment.update', editingComment.value.id), editingComment.value)
        .then(({data}) => {
            editingComment.value = null
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id) {
                    return data
                }
                return c
            })
        })
}

function sendCommentReaction(comment) {
    instance.post(route('comment.reaction', comment.id), {
        type: 'like'
    })
        .then(({data}) => {
            comment.current_user_has_reaction = data.current_user_has_reaction
            comment.num_of_reactions = data.num_of_reactions
        })
}

function startCommentEdit(comment) {
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
    }
}

function onCommentCreate(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments++

    }
    emit('createComment', comment)
}

function onCommentDelete(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments--
    }
    emit('createDelete', comment)

}
</script>

<template>
    <div class="flex  gap-2 mb-3">
        <Link :href="route('profile',authUser.username)">
            <img :src="authUser.avatar_url"
                 class="w-[40px] rounded-full  border-2 transition-all hover:border-blue-500">
        </Link>
        <div class="flex flex-1">
            <InputTextarea v-model="newCommentText"
                           class="w-full max-h-[160px]  resize-none rounded-r-none"
                           placeholder="Enter your comment here"
                           rows="1"></InputTextarea>
            <IndigoButton class="w-[100px]  rounded-l-none" @click="createComment">
                Submit
            </IndigoButton>
        </div>
    </div>
    <div>
        <div v-for="comment of data.comments" :key="comment.id" class="mb-4">
            <div class="flex justify-between gap-2">
                <div class="flex gap-2">
                    <a href="javascript:void(0)">
                        <img :src="comment.user.avatar_url"
                             class="w-[40px]  rounded-full  border-2 transition-all hover:border-blue-500">
                    </a>
                    <div>
                        <h4 class="font-bold">
                            <a class="hover:underline" href="javascript:void(0)">
                                {{ comment.user.name }}
                            </a>
                        </h4>
                        <small class="text-gray-400 text-xs">{{ comment.updated_at }}</small></div>
                </div>
                <EditDeleteDropdown :comment="comment" :post="post" :user="comment.user"
                                    @delete="deleteComment(comment)"
                                    @edit="startCommentEdit(comment)"/>
            </div>
            <div class="pl-12">
                <div v-if="editingComment && editingComment.id === comment.id">
                    <InputTextarea v-model="editingComment.comment"
                                   class="w-full max-h-[160px]  resize-none "
                                   placeholder="Enter your comment here"
                                   rows="1"></InputTextarea>
                    <div class="flex gap-2 justify-end ">
                        <button class="rounded-r-none text-indigo-500"
                                @click="editingComment = null">
                            Cancel
                        </button>
                        <IndigoButton class="w-[100px]  " @click="updateComment">
                            Update
                        </IndigoButton>
                    </div>
                </div>
                <ReadMoreReadLess v-else :content="comment.comment"
                                  content-class="text-sm flex flex-1 "/>
                <Disclosure>
                    <div class="mt-1 flex gap-2">
                        <button
                            :class="[
                                        comment.current_user_has_reaction ?
                                        'bg-indigo-50 hover:bg-indigo-100' :
                                        '   hover:bg-indigo-200',

                                    ]"
                            class="flex items-center text-indigo-500 text-xs py-0.5 px-1 rounded-lg"
                            @click="sendCommentReaction(comment)">
                            <HandThumbUpIcon class="w-3 h-3 mr-1"/>
                            <span class="mr-2">{{ comment.num_of_reactions }}</span>
                            {{ comment.current_user_has_reaction ? 'unlike' : 'like' }}

                        </button>
                        <DisclosureButton
                            class="flex items-center text-xs text-indigo-500  py-0.5 px-1
                                 hover:bg-indigo-100 rounded-lg">
                            <ChatBubbleLeftEllipsisIcon class="w-3 h-3 mr-1"/>
                            <span class="mr-2">{{ comment.num_of_comments }}</span>

                            comments
                        </DisclosureButton>
                    </div>
                    <DisclosurePanel class="mt-3">
                        <CommentList
                            :data="{comments: comment.comments}"
                            :parent-comment="comment"
                            :post="post"
                            @comment-create="onCommentCreate"
                            @comment-delete="onCommentDelete"/>
                    </DisclosurePanel>
                </Disclosure>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
