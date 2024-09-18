<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="data in subjects">
            <div class="accordion js-accordion accordion--boxed list-group-flush"
                 id="parent">
                <div class="accordion__item">
                    <a href="#"
                       class="accordion__toggle"
                       data-toggle="collapse"
                       :data-target="'#course-toc-'+data.code"
                       data-parent="#parent">
                        <div class="d-flex mr-3 align-items-center">
                            <div class="avatar avatar-md mr-12pt"><a href=""><img src="/images/blank.webp" alt="avatar" class="avatar-img rounded"></a></div>
                            <!-- <span class="flex mr-4">{{ data.code }}</span> -->
                            <div class="flex mr-4">
                                <span class="card-title" href="javascript:void(0)">{{ data.name }}</span>
                                <div class="card-subtitle text-50">{{ data.code }}</div>
                            </div>
                        </div>
                        <div class="flex mr-3"></div>
                        <span class="text-80 text-uppercase d-flex align-items-center " :class="finalGrade(data.midterm.grade,data.finals.grade) > 3 ? 'text-danger' : 'text-primary'">Grade: {{ finalGrade(data.midterm.grade,data.finals.grade) }}</span>
                        <span class="accordion__toggle-icon material-icons mr-2">keyboard_arrow_down</span>
                    </a>
                    <div class="accordion__menu collapse"
                            :id="'course-toc-'+data.code">
                            <div class="accordion__menu-link">
                                <div class="accordion col-md-12" id="child">
                                    <div class="accordion__item border-0 shadow-none">
                                        <a href="#"
                                        class="accordion__toggle"
                                        data-toggle="collapse"
                                        :data-target="'#term-'+data.code"
                                        data-parent="#child">
                                            <span class="flex mr-4"><strong>Midterm</strong></span>
                                            <span class="text-80 text-uppercase d-flex align-items-center " :class="data.midterm.grade > 3 ? 'text-danger' : 'text-primary'">Grade: {{ data.midterm.grade }}</span>
                                            <div class="flex mr-3"></div>
                                            <span class="accordion__toggle-icon material-icons mr-2">keyboard_arrow_down</span>
                                        </a>
                                        <div v-if="data.midterm.criteria.length != 0">
                                            <div class="accordion__menu collapse" :id="'term-'+data.code" v-for="criteria in data.midterm.criteria">
                                                <div class="accordion__menu-link">
                                                    <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                        <i class="material-icons icon-16pt">hourglass_empty</i>
                                                    </span>
                                                    <a class="flex" href="javascript:void(0)"><strong>{{ criteria.name }}</strong></a>
                                                    <div class="ml-auto">
                                                            <strong>{{ criteria.total_score }}/{{ criteria.over }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion__menu collapse" :id="'term-'+data.code" v-else>
                                            <div class="accordion__menu-link">
                                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                    <i class="material-icons icon-16pt">hourglass_empty</i>
                                                </span>
                                                <a class="flex" href="javascript:void(0)"><strong>No Data Found</strong></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                           </div>
                           <div class="accordion__menu-link">
                                <div class="accordion col-md-12" id="child">
                                    <div class="accordion__item border-0 shadow-none">
                                        <a href="#"
                                        class="accordion__toggle"
                                        data-toggle="collapse"
                                        :data-target="'#termfin-'+data.code"
                                        data-parent="#child">
                                            <span class="flex mr-4"><strong>Finals</strong></span>
                                            <span class="text-80 text-uppercase d-flex align-items-center " :class="data.finals.grade > 3 ? 'text-danger' : 'text-primary'">Grade: {{ data.finals.grade }}</span>
                                            <div class="flex mr-3"></div>
                                            <span class="accordion__toggle-icon material-icons mr-2">keyboard_arrow_down</span>
                                        </a>
                                        <div v-if="data.finals.criteria.length" class="accordion__menu collapse" :id="'termfin-'+data.code" v-for="criteria in data.finals.criteria">
                                            <div class="accordion__menu-link">
                                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                    <i class="material-icons icon-16pt">hourglass_empty</i>
                                                </span>
                                                <a class="flex" href="javascript:void(0)"><strong>{{ criteria.name }}</strong></a>
                                                <div class="ml-auto">
                                                        <strong>{{ criteria.total_score }}/{{ criteria.over }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion__menu collapse" :id="'termfin-'+data.code" v-else>
                                            <div class="accordion__menu-link">
                                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                    <i class="material-icons icon-16pt">hourglass_empty</i>
                                                </span>
                                                <a class="flex" href="javascript:void(0)"><strong>No Data Found</strong></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                           </div>
                           <!-- <div class="accordion__menu-link" v-for="criteria in data.criteria">
                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                    <i class="material-icons icon-16pt">hourglass_empty</i>
                                </span>
                                <a class="flex" href="javascript:void(0)"><strong>{{ criteria.name }}</strong></a>
                               <div class="ml-auto">
                                    <strong>{{ criteria.total_score }}</strong>
                               </div>
                           </div>
                           <div class="accordion__menu-link">
                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                    <i class="material-icons icon-16pt">hourglass_empty</i>
                                </span>
                                <a class="flex" href="javascript:void(0)"><strong>Midterm Grade</strong></a>
                               <div class="ml-auto">
                                    <strong>2.5</strong>
                               </div>
                           </div>
                           <div class="accordion__menu-link">
                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                <i class="material-icons icon-16pt">hourglass_empty</i>
                                </span>
                                <a class="flex" href="javascript:void(0)"><strong>Finals Grade</strong></a>
                               <div class="ml-auto">
                                    <strong>2.5</strong>
                               </div>
                           </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    subjects: Object,
});


function finalGrade(mid, fin){
    const final = (parseInt(mid) + parseInt(fin)) / 2;
    const readableGrade = 0;

    if (final > 3) {
        return Math.round(final * 2) / 2;
    } else {
        return Math.round(final * 4) / 4;
    }
}


</script>