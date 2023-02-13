


<template>
 <div>
    
    <v-app id="inspire">
        <v-main>
        <v-navigation-drawer>
      <v-sheet color="grey-lighten-4" class="pa-4">
        <div></div>
      </v-sheet>

      <v-divider></v-divider>
      <v-list>
        <v-list-item>
          <template v-slot:prepend>
          </template>
          <v-dialog v-model="dialog" persistent width="1024">
      <template v-slot:activator="{ props }">
        <v-btn color="primary" v-bind="props">
          Ajouter un cours 
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
        <span class="text-h5">Ajouter un cours</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  label="Titre du cours"
                  id="titre"
                  required
                  v-model="titleRef"
                ></v-text-field>
              </v-col>


              <v-col cols="12">
                <v-text-field
                  label="Categorie du cours"
                  type="text"
                  id="categorie"
                  required
                  v-model="categoryRef"
                ></v-text-field>
              </v-col>


             
              <v-col cols="12">
              <v-textarea label="Label"
              id="description"
              required
              v-model="descriptionRef"
              ></v-textarea>
              
              </v-col>

              
              <v-col col="12">
              <v-file-input
                v-model="selectedFile"
                @change="uploadFile"
                ref="fileInput"
                :rules="rules"
              /></v-col>

              

            </v-row>
          </v-container>
          <small>*indique les champs obligatoires </small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue-darken-1"
            variant="text"
            @click="dialog = false"
          >
            Fermer
          </v-btn>
          <v-btn color="blue-darken-1" variant="text"
            @click="addCourse"
          >
            Ajouter
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Liste des cours  -->
      <v-container class="py-8 px-6" fluid>
        <v-row>
          <v-col cols="12">
            <v-card>
              <v-list lines="two">
                <v-list-subheader>Mes Cours</v-list-subheader>
                <template v-for="course in courses" :key="course">
                  <v-row class="justify-center">
                  <span>{{ course.title }}</span>
                  </v-row>
                  <v-list-item>
                    <v-list-item-title>{{ n }}</v-list-item-title>
                  </v-list-item>
                  <v-divider
                    v-if="n !== 6"
                    :key="`divider-${n}`"
                    inset
                  ></v-divider>
                </template>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
<v-main>
</v-main>
</div>
</template>
<script>
import useApi from '../hooks/useApi';
import { onMounted, ref } from 'vue';
import useUser from '../hooks/useUser'

  export default {
    data: () => ({
      dialog: false,
      titleRef: ref(),
      descriptionRef: ref(),
      categoryRef: ref(),
      api: useApi(),
      cards: ['Mes Cours', 'Mes Cours Validés'], 
      courses: ref([]),
      user: useUser()
    }),
    methods: {
    async postNewCourse() {
        const data = this.api.addCourse({
          title: this.titleRef.value,
          description: this.descriptionRef.value,
          category: this.categoryRef.value,
        });
    },
      async addCourse(id) {
        await this.postNewCourse({
          title: this.titleRef.value,
          description: this.descriptionRef.value,
          category: this.categoryRef.value,
          upload : this.upload 
        });
        }, 
  
    async fetchComments() {
      try {
         this.courses = await this.api.getCoursesByCreatorId(this.user.user.id)
            console.log(this.user.user.id)
          } catch (error) {
        console.error("error fetching comments");
        console.log()
       }
      }
    },

    mounted() {
        this.fetchComments()
      },
  }
</script>
