<template>
  <v-hover v-slot="{ hover }">
    <v-card class="card" :elevation="hover ? 16 : 2" :class="{ 'on-hover': hover }">
      <router-link :to="`/esgi-challenge/course/${course.id}`">
        <v-img :src="thumbnail" alt="" class="thumbnail"></v-img>
      </router-link>
      <v-card-title>{{ course.title }}</v-card-title>
      <v-card-subtitle class="description">{{ course.description }}</v-card-subtitle>
      <v-card-text>
        <v-row align="center" class="mx-0">
          <div class="grey--text">
            {{ createdAt }}
          </div>
        </v-row>
      </v-card-text>
      <div class="price-container">
        <stripe-checkout ref="checkoutRef" :session-id="sessionId" :pk="publishableKey" @loading="v => loading = v" />
        <v-btn @click="createSession" class="price" variant="tonal" color="primary">Acheter - {{ course.price
        }}$</v-btn>
      </div>
    </v-card>
  </v-hover>
</template>

<script>
import { StripeCheckout } from '@vue-stripe/vue-stripe';
import useApi from '../../hooks/useApi';
import useUser from '../../hooks/useUser';

export default {
  components: {
    StripeCheckout,
  },
  props: {
    course: {
      required: true,
    }
  },
  methods: {
    submit() {
      this.$refs.checkoutRef.redirectToCheckout();
    },
    async createStripeSession() {
      let response

      try {
        response = await this.api.getStripeSessionId(
          this.userData.id,
          this.course.id,
          import.meta.env.APP_VITE_FRONT_URL + 'payment/success',
          import.meta.env.APP_VITE_FRONT_URL + 'payment/cancel'
        )

      } catch (error) {
        console.log("error", error)
      }

      console.log("response", response?.id)
      return response?.id

    },
    async createSession() {
      const sessionId = await this.createStripeSession();
      this.sessionId = sessionId;
      this.$refs.checkoutRef.redirectToCheckout();
    },
  },
  data() {
    this.api = useApi()
    this.userData = useUser()
    this.publishableKey = import.meta.env.APP_STRIPE_PUBLISHABLE_KEY
    return {
      loading: false,
      sessionId: null,
    };
  },
  computed: {
    thumbnail() {
      const thumbnailUrl = this.course.thumbnailUrl;

      if (thumbnailUrl.startsWith('/thumbnails/https://')) {
        return thumbnailUrl.substring('/thumbnails/'.length);
      } else if (thumbnailUrl.startsWith('/thumbnails/http://')) {
        return thumbnailUrl.substring('/thumbnails/'.length);
      } else {
        return thumbnailUrl ?? '';
      }
    },
    createdAt() {
      const createdAt = new Date(this.course.createdAt).toLocaleDateString('fr-Fr', {
        weekday: "long", year: "numeric", month: "short", day: "numeric"
      })
      return createdAt
    }
  }
}
</script>

<style scoped>
.card {
  width: 100%;
  height: 100%;
  overflow: hidden;
  transition: all 0.3s ease;
}

.card:hover {
  transform: scale(1.06);
}

.thumbnail {
  height: 200px;
  width: 100%;
  object-fit: cover;
}

.price-container {
  display: flex;
  height: 100%;
}

.price {
  width: 100%;
}
</style>