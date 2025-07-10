(function() {
    if (!window.apiConfig) {
        window.apiConfig = {
            baseUrl: '/api',
            endpoints: {
                login: '/auth/login',
                logout: '/auth/logout',
                forgotPassword: '/auth/forgot-password',
                verifyOtp: '/auth/verify-otp',
                resetPassword: '/auth/reset-password',
                registerStudent: '/auth/register/student',
                registerMentor: '/auth/register/mentor',
            },
        };
    }
})();