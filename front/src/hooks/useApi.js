import { inject, ref, watch } from "vue";
import { useRouter } from 'vue-router';
import axios from "axios";
import { APP_ROUTES } from '../utils/constants';

const axiosInstance = axios.create({
  baseURL: "http://localhost:8000/api/",
});

function getRequestHeaders(
  options = { token: null, withBody: false, contentType: "application/json" }
) {
  const { token, withBody, contentType } = options;

  return {
    "Content-Type": withBody ? contentType : undefined,
    Authorization: token != null ? `Bearer ${token}` : undefined,
  };
}

const apiClient = {
  get: async function (url, token = null) {
    const response = await axiosInstance.get(url, {
      headers: getRequestHeaders({ token: token }),
    });
    const contentType = response.headers["content-type"] ?? "";
    if (
      contentType.indexOf("application/ld+json") !== -1 &&
      response.data["@type"] === "hydra:Collection"
    ) {
      return response.data["hydra:member"];
    }
    return response.data;
  },

  post: async function (
    url,
    options = { data: null, contentType: "application/json", token: null }
  ) {
    const { data, contentType, token } = options;
    const response = await axiosInstance.post(url, data, {
      headers: getRequestHeaders({
        token: token,
        withBody: data != null,
        contentType: contentType,
      }),
    });
    return response.data;
  },

  put: async function (
    url,
    options = { data: null, contentType: "application/json", token: null }
  ) {
    const { data, contentType, token } = options;
    const response = await axiosInstance.put(url, data, {
      headers: getRequestHeaders({
        token: token,
        withBody: data != null,
        contentType: contentType,
      }),
    });
    return response.data;
  },

  patch: async function (
    url,
    options = { data: null, contentType: false, token: null }
  ) {
    const { data, contentType, token } = options;
    const response = await axiosInstance.patch(url, data, {
      headers: getRequestHeaders({
        token: token,
        withBody: data != null,
        contentType: contentType,
      }),
    });
    return response.data;
  },

  delete: function (url, token = null) {
    return axiosInstance.delete(url, {
      headers: getRequestHeaders({ token: token }),
    });
  },
  
  getFile: async function(url, token = null) {
    const response = await axiosInstance.get(url, {
      headers: getRequestHeaders({ token: token }),
      responseType: "blob"
    });
    return URL.createObjectURL(response.data);
  },
};

function constructRequestUrl(endpoint, params = null) {
  const keys = params != null ? Object.keys(params) : [];
  return `${endpoint}${
    keys.length > 0 ? "?" + keys.map((k) => `${k}=${params[k]}`).join("&") : ""
  }`;
}

export default function useApi() {
  
  const { state, actions } = inject("store");
  const userRef = ref({ ...state.user });
  const router = useRouter()
  
  // handle token expired
  axiosInstance.interceptors.response.use(
    response => response,
    (error)=>{
      if(error.response.status === 401) {
        actions.logout()
        router.push({name: APP_ROUTES.login, replace: true})
      } else {
        return Promise.reject(error)
      }
    }
  )

  watch(
    () => state.user,
    (newState) => {
      userRef.value = { ...newState };
    }
  );

  function login(email, password) {
    return apiClient.post(constructRequestUrl("login"), {
      data: {
        email: email,
        password: password,
      },
    });
  }

  function getAllUsers() {
    return apiClient.get(constructRequestUrl("users"), userRef.value?.token);
  }
  function removeUser(userId) {
    return apiClient.delete(
      constructRequestUrl(`users/${userId}`),
      userRef.value?.token
    );
  }
  function editUser(userId, changedProperties) {
    return apiClient.patch(constructRequestUrl(`users/${userId}`), {
      data: { ...changedProperties },
      contentType: "application/merge-patch+json",
      token: userRef.value?.token,
    });
  }

  function getAllCategories() {
    return apiClient.get(
      constructRequestUrl("categories"),
      userRef.value?.token
    );
  }

  function getAllCourses() {
    return apiClient.get(
      constructRequestUrl("contents?order[createdAt]=desc"),
      userRef.value?.token
    );
  }

  function getCourseByCategoryId(id) {
    return apiClient.get(
      constructRequestUrl("contents?order[createdAt]=desc&categoryId=" + id),
      userRef.value?.token
    );
  }

  function getCourseById(id) {
    return apiClient.get(
      constructRequestUrl("contents/" + id),
      userRef.value?.token
    );
  } 

  function getCoursesByCreatorId(creator_id) {
    return apiClient.get(
      constructRequestUrl("contents?page=1&creatorId%5B%5D="+ creator_id),
      userRef.value?.token
    );
  }

  function getAllCourses() {
    return apiClient.get(constructRequestUrl("contents"), userRef.value?.token);
  }

  function postReportContent(data) {
    const { description, reporterId, contentId } = data;
    return apiClient.post(constructRequestUrl("reported_contents"), {
      data: {
        reporterId: "/api/users/" + reporterId,
        contentId: "/api/contents/" + contentId,
        description: description,
      },
      token: userRef.value?.token,
    });
  }

  function postComment(data) {
    const { comment, courseId, commenterId } = data;
    return apiClient.post(constructRequestUrl("comments"), {
      data: {
        commenterId: "/api/users/" + commenterId,
        content: comment,
        course: "/api/contents/" + courseId,
      },
      token: userRef.value?.token,
    });
  }

  function getCommentsByCourse(id) {
    return apiClient.get(
      constructRequestUrl("comments?course=" + id + "&order[createdAt]=desc"),
      userRef.value?.token
    );
  }

  function getAllComments() {
    return apiClient.get(constructRequestUrl("comments"), userRef.value?.token);
  }

  function register(email, password, firstname, lastname) {
    const res = apiClient.post(constructRequestUrl("register"), {
      data: {
        firstname: firstname,
        lastname: lastname,
        email: email,
        plainPassword: password,
      },
    });
    return res;
  }

  function addCourse(title,description,category){
    const res = apiClient.post(constructRequestUrl("contents"), {
      data: {
        title: title,
        description: description,
        category: category,
      },
    });
    return res;
  }

  function removeComment(commentId) {
    return apiClient.delete(
      constructRequestUrl(`comments/${commentId}`),
      userRef.value?.token
    );
  }

  function verifyRegistration(signedUrl) {
    return apiClient.get(signedUrl)
  }

  function getBeReviewerApplications() {
    return apiClient.get(constructRequestUrl("be_reviewer_applications"), userRef.value?.token)
  }

  function acceptBeReviwerApplication(applicationId) {
    return apiClient.patch(constructRequestUrl(`be_reviewer_applications/${applicationId}`), {
      data: {
        status: 'ACCEPTED'
      },
      token: userRef.value?.token,
      contentType: "application/merge-patch+json",
    })
  }

  function refuseBeReviwerApplication(applicationId) {
    return apiClient.patch(constructRequestUrl(`be_reviewer_applications/${applicationId}`), {
      data: {
        status: 'REFUSED'
      },
      token: userRef.value?.token,
      contentType: "application/merge-patch+json",
    })
  }

  function publishCourse(courseId) {
    return apiClient.patch(constructRequestUrl(`contents/${courseId}`), {
      data: {
        active: true
      },
      token: userRef.value?.token,
      contentType: "application/merge-patch+json",
    })
  }

  function getCourseVideo(videoUrl) {
    return apiClient.getFile(videoUrl, userRef.value?.token)
  }

  function getReviewers() {
    return apiClient.get('users', userRef.value?.token)
  }

  function sendValidationRequest(reviewerId, courseId) {
    return apiClient.post('validation_requests', {
      data: {
        reviewerId: `/api/users/${reviewerId}`,
        contentId: `/api/contents/${courseId}`,
      },
      contentType: 'application/ld+json',
      token: userRef.value?.token,
    })
  }

  function getValidationRequestsByCourseId(courseId) {
    return apiClient.get(constructRequestUrl('validation_requests', {contentId: courseId}), userRef.value?.token)
  }

  function getActiveValidationRequests() {
    return apiClient.get(constructRequestUrl('validation_requests', {active: true}), userRef.value?.token)
  }

  return {
    login,
    getAllUsers,
    editUser,
    removeUser,
    getCourseById,
    getAllCourses,
    getAllComments,
    register,
    getCommentsByCourse,
    postReportContent,
    addCourse,
    getCoursesByCreatorId,
    getAllCategories,
    getCourseByCategoryId,
    postComment,
    removeComment,
    verifyRegistration,
    getBeReviewerApplications,
    acceptBeReviwerApplication,
    refuseBeReviwerApplication,
    publishCourse,
    getCourseVideo,
    getReviewers,
    sendValidationRequest,
    getValidationRequestsByCourseId,
    getActiveValidationRequests,
  };
}
