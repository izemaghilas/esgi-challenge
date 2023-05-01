<template>
  <v-hover v-slot="{ hover }">
    <v-card class="card" :elevation="hover ? 16 : 2" :class="{ 'on-hover': hover }">
      <router-link :to="`/esgi-challenge/course/${course.id}`">
        <v-img :src="thumbnail" alt="" class=""></v-img>
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
        <stripe-checkout ref="checkoutRef" mode="payment" :pk="publishableKey" :line-items="lineItems"
          :success-url="successURL" :cancel-url="cancelURL" @loading="v => loading = v" />
        <v-btn @click="submit" class="price" variant="tonal" color="primary">Acheter - {{ course.price }}$</v-btn>
      </div>
    </v-card>

  </v-hover>
</template>

<script>
import { StripeCheckout } from '@vue-stripe/vue-stripe';

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
  },
  data() {
    this.publishableKey = process.env.STRIPE_PUBLISHABLE_KEY;
    return {
      loading: false,
      lineItems: [
        {
          price: process.env.PRICE,
          quantity: 1,
        },
      ],
      successURL: FRONT_URL + 'course/' + this.course.id,
      cancelURL: FRONT_URL + 'cancelled',
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
</style>