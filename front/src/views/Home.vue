<template>
    <div>
        <Courses heading="Les cours mis à jour récemment" />
    </div>
</template>

<script>
import Courses from '../components/user/Courses.vue'

export default {
    name: 'Home',
    components: {
        Courses,
    }


}
</script>