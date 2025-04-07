<script setup>
defineProps({
    show: Boolean
})
</script>


<template>
        <Transition
            enter-from-class="transition duration-200 opacity-0"
            enter-to-class="transition duration-300 opacity-100"
            leave-from-class="transition duration-300 opacity-100"
            leave-to-class="transition duration-200 opacity-0"
        >
            <div v-if="show" class="modal-mask">
                <div class="modal-container">
                    <header>
                        <slot name="header">default header</slot>
                    </header>
                    <div>
                        <slot>default body</slot>
                    </div>

                    <footer class="modal-custom-footer">
                        <button @click="$emit('close')" class="close-button me-1">Close</button>
                        <slot name="footer">
                            <button class="submit-button">
                                Submit
                            </button>
                        </slot>

                    </footer>

                </div>
            </div>
        </Transition>

</template>

<style>

.modal-mask {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: grid;
    place-items: center;
}

.modal-container {
    background: white;
    padding: 1rem;
    width: 80vw;
    max-width: 500px;
    border-radius: 10px;
}

.modal-custom-footer {
    border-top: 1px solid #ddd;
    margin-top: 1rem;
    padding-top: 0.5rem;
    font-size: .8rem;
}

.modal-custom-footer .close-button, .submit-button {
    color: #333;
    background: #ddd;
    padding: .25rem .75rem;
    border-radius: 15px;
}

.modal-custom-footer .close-button:hover {
    background: #c8c8c8;
}

.modal-custom-footer .submit-button:hover {
    background: #6f42c1;
    color: #fff;
}
.transition {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.opacity-0 {
    opacity: 0;
}
.opacity-100 {
    opacity: 100;
}

.duration-200 {
    transition-duration: 200ms;
}
.duration-300 {
    transition-duration: 300ms;
}

</style>
