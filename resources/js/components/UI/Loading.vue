<template>
        <div class="d-flex align-items-center justify-content-center mb-2 me-2">
            <div class="rot">
            </div>
            <span class="ms-2">
                {{ $t('additional.Loading') }}
            </span>
        </div>
</template>

<script>

export default {
    name: "Loading",

    props: {
        height: {
            type: String,
        },
        width: {
            type: String,
        },
        textSize: {
            type: String,
        },
        modelSize: {
            type: String,
        }
    }
}
</script>

<style scoped>

    span{
        font-size: v-bind(textSize);
    }
    .rot{
        width: v-bind(width);
        height: v-bind(height);
        border-radius: 50%;
        clip-path: polygon(50% -1%, 93% 22%, 93% 78%, 50% 101%, 7% 78%, 7% 22%);
        position: relative;
        display: inline-block;
        overflow: hidden;
        font-size: v-bind(modelSize);;
    }

    .rot::before, .rot::after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
    }

    .rot::before {
        background:
            radial-gradient(circle at 50%  3%, blue,  transparent 25%),
            radial-gradient(circle at  3% 50%, green, transparent 25%),
            radial-gradient(circle at 97% 50%, rgb(122, 0, 128), transparent 25%),
            radial-gradient(circle at 50% 97%, red,   transparent 25%);
        animation: spin 5s linear infinite;
    }

    .rot::after {
        margin: .25em;
        background: rgba(237,198,156,1);
        clip-path: inherit;
        border-radius: inherit;
    }

    @keyframes spin {
        from { transform: rotate(0)     }
        to   { transform: rotate(1turn) }
    }
</style>