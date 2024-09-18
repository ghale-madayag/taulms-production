<template>
    <div class="card-footer p-8pt">
<!--        <ul class="pagination justify-content-start pagination-xsm m-0">-->
<!--            <template v-for="link in links">-->
<!--                <li class="page-item" :class="link.url ? '' : 'disabled'">-->
<!--                    <Component :is="link.url ? 'Link' : 'span'" :class="link.active ? 'text-white bg-primary' : ''" class="page-link" :href="link.url" preserve-scroll>-->
<!--                        <span v-html="link.label"></span>-->
<!--                    </Component>-->
<!--                </li>-->
<!--            </template>-->

<!--        </ul>-->
        <ul class="pagination justify-content-start pagination-xsm m-0">
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true" class="material-icons">chevron_left</span>
                    <span>Prev</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Page 1">
                    <span>1</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Page 2">
                    <span>2</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                    <span>Next</span>
                    <span aria-hidden="true" class="material-icons">chevron_right</span>
                </a>
            </li>
        </ul>
    </div>
</template>

<script setup>

</script>

