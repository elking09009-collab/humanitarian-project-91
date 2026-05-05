const API_BASE_URL = localStorage.getItem("apiBaseUrl") || "https://humanitarian-backend.onrender.com/api";

function getAdminPanelUrl() {
    return API_BASE_URL.replace(/\/api\/?$/, "") + "/admin";
}

function getAuthToken() {
    return localStorage.getItem("authToken");
}

async function apiRequest(path, method = "GET", body = null, auth = false) {
    const headers = {
        "Accept": "application/json",
    };

    if (body) {
        headers["Content-Type"] = "application/json";
    }

    if (auth) {
        const token = getAuthToken();
        if (!token) {
            throw new Error("يجب تسجيل الدخول أولاً");
        }

        headers["Authorization"] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE_URL}${path}`, {
        method,
        headers,
        body: body ? JSON.stringify(body) : null,
    });

    const contentType = response.headers.get("content-type") || "";
    const payload = contentType.includes("application/json")
        ? await response.json()
        : await response.text();

    if (!response.ok) {
        if (payload && typeof payload === "object" && payload.message) {
            throw new Error(payload.message);
        }

        if (payload && typeof payload === "object" && payload.errors) {
            const firstKey = Object.keys(payload.errors)[0];
            if (firstKey && payload.errors[firstKey]?.[0]) {
                throw new Error(payload.errors[firstKey][0]);
            }
        }

        throw new Error("حدث خطأ أثناء الاتصال بالخادم");
    }

    return payload;
}

window.addEventListener("load", function () {
    const loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = "none";
    }
});

const counters = document.querySelectorAll(".counter");

if (counters.length > 0) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute("data-target");
                let current = 0;

                const updateCounter = () => {
                    const increment = target / 100;
                    if (current < target) {
                        current += increment;
                        counter.innerText = Math.ceil(current);
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCounter();
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

const cards = document.querySelectorAll(".card");

if (cards.length > 0) {
    window.addEventListener("load", () => {
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add("show");
            }, index * 300);
        });
    });
}

const donationForm = document.getElementById("donationForm");

if (donationForm) {
    donationForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const name = document.getElementById("name");
        const email = document.getElementById("email");
        const amount = document.getElementById("amount");

        if (name && email && amount && name.value && email.value && amount.value) {
            alert("شكراً لتبرعك!");
            donationForm.reset();
        } else {
            alert("من فضلك املأ كل الحقول");
        }
    });
}

const inputs = document.querySelectorAll(".donation-form input");

if (inputs.length > 0) {
    inputs.forEach(input => {
        input.addEventListener("focus", () => {
            input.style.borderColor = "#2e7d32";
        });
        input.addEventListener("blur", () => {
            input.style.borderColor = "#ccc";
        });
    });
}

const donationTypeSelect = document.getElementById("donationType");

if (donationTypeSelect) {
    donationTypeSelect.addEventListener("change", function () {
        if (this.value) {
            window.location.href = this.value;
        }
    });
}

function goToDonation() {
    const select = document.getElementById("donationType");

    if (select && select.value) {
        window.location.href = select.value;
    } else {
        alert("اختاري نوع التبرع الأول");
    }
}

const forms = document.querySelectorAll(".details-form form");

forms.forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const data = {};
        const formInputs = form.querySelectorAll("input");

        formInputs.forEach(input => {
            data[input.placeholder] = input.value;
        });

        localStorage.setItem("donationData", JSON.stringify(data));
        showSuccessMessage();
        form.reset();
    });
});

function showSuccessMessage() {
    const msg = document.createElement("div");
    msg.className = "success-message";
    msg.innerText = "✔ تم إرسال طلبك بنجاح";

    document.body.appendChild(msg);

    setTimeout(() => {
        msg.classList.add("show");
    }, 100);

    setTimeout(() => {
        msg.remove();
    }, 3000);
}

const faders = document.querySelectorAll(".fade-in");

if (faders.length > 0) {
    const appear = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    });

    faders.forEach(el => appear.observe(el));
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        alert("المتصفح لا يدعم الموقع");
    }
}

function showPosition(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;

    const locationInput = document.getElementById("location");
    if (locationInput) {
        locationInput.value = `Lat: ${lat} , Lng: ${lng}`;
    }

    const map = document.getElementById("map");
    if (map) {
        map.innerHTML = `<iframe width="100%" height="250" src="https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed"></iframe>`;
    }
}

window.getLocation = getLocation;
window.goToDonation = goToDonation;

const loginForm = document.getElementById("loginForm");
const fillAdminBtn = document.getElementById("fillAdminBtn");

if (fillAdminBtn) {
    fillAdminBtn.addEventListener("click", function () {
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");

        if (emailInput && passwordInput) {
            emailInput.value = "admin@humanitarian.local";
            passwordInput.value = "admin1234";
        }
    });
}

if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");

        const submitButton = loginForm.querySelector("button[type='submit']");
        submitButton.disabled = true;
        submitButton.textContent = "جاري تسجيل الدخول...";

        try {
            const result = await apiRequest("/login", "POST", {
                email: emailInput.value,
                password: passwordInput.value,
            });

            localStorage.setItem("authToken", result.token);
            localStorage.setItem("user", JSON.stringify(result.user));

            alert("تم تسجيل الدخول بنجاح");

            if (result.user?.role === "admin") {
                window.location.href = getAdminPanelUrl();
            } else {
                window.location.href = "index.html";
            }
        } catch (error) {
            const demoBox = document.getElementById("demoBox");
            // إذا كان الخطأ بسبب عدم وصول الـ backend، أظهر زرار الوضع التجريبي
            if (demoBox && (error.message === "Failed to fetch" || error.message.includes("fetch") || error.message.includes("الاتصال"))) {
                demoBox.style.display = "block";
                alert("تعذّر الاتصال بالخادم.\nيمكنك تجربة لوحة التحكم بالوضع التجريبي أدناه.");
            } else {
                alert(error.message || "فشل تسجيل الدخول");
            }
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "دخول";
        }
    });
}

async function loadAreas() {
    const areaSelect = document.getElementById("area");
    if (!areaSelect) {
        return;
    }

    try {
        const areas = await apiRequest("/areas");

        areas.forEach(area => {
            const option = document.createElement("option");
            option.value = area.id;
            option.textContent = area.name;
            areaSelect.appendChild(option);
        });
    } catch (error) {
        alert("تعذر تحميل المناطق من الخادم");
    }
}

const requestForm = document.getElementById("requestForm");

if (requestForm) {
    loadAreas();

    requestForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const areaInput = document.getElementById("area");
        const needInput = document.getElementById("need");
        const quantityInput = document.getElementById("quantity");
        const notesInput = document.getElementById("notes");

        const submitButton = requestForm.querySelector("button[type='submit']");
        submitButton.disabled = true;
        submitButton.textContent = "جاري الإرسال...";

        try {
            await apiRequest(
                "/needs",
                "POST",
                {
                    area_id: Number(areaInput.value),
                    type: needInput.value,
                    quantity: Number(quantityInput.value),
                    notes: notesInput.value,
                },
                true
            );

            alert("تم إرسال الطلب بنجاح");
            requestForm.reset();
        } catch (error) {
            alert(error.message || "فشل إرسال الطلب");
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "إرسال الطلب";
        }
    });
}

function showMessage(e) {
    e.preventDefault();

    const successMsg = document.getElementById("successMsg");
    if (successMsg) {
        successMsg.style.display = "block";
    }
}

window.showMessage = showMessage;

const registerForm = document.getElementById("registerForm");

if (registerForm) {
    registerForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const errorBox = document.getElementById("reg-error");
        errorBox.style.display = "none";

        const submitButton = registerForm.querySelector("button[type='submit']");
        submitButton.disabled = true;
        submitButton.textContent = "جاري الإنشاء...";

        try {
            const result = await apiRequest("/register", "POST", {
                name:     document.getElementById("reg-name").value,
                email:    document.getElementById("reg-email").value,
                phone:    document.getElementById("reg-phone").value,
                role:     document.getElementById("reg-role").value,
                password: document.getElementById("reg-password").value,
            });

            alert(result.message || "تم إرسال طلب إنشاء الحساب للأدمن للمراجعة");
            window.location.href = "login.html";
        } catch (error) {
            errorBox.textContent = error.message || "فشل إنشاء الحساب";
            errorBox.style.display = "block";
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "إنشاء حساب";
        }
    });
}

const statusForm = document.getElementById("statusForm");

if (statusForm) {
    statusForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const email = document.getElementById("status-email")?.value;
        const statusResult = document.getElementById("statusResult");

        if (!statusResult) {
            return;
        }

        try {
            const result = await apiRequest("/registration-status", "POST", { email });

            const statusMap = {
                pending: "قيد المراجعة",
                approved: "مقبول",
                rejected: "مرفوض",
            };

            let html = `<strong>الحالة:</strong> ${statusMap[result.status] || result.status}`;

            if (result.status === "rejected" && result.reason) {
                html += `<br><strong>سبب الرفض:</strong> ${result.reason}`;
            }

            statusResult.innerHTML = html;
            statusResult.style.display = "block";
        } catch (error) {
            statusResult.innerHTML = error.message || "تعذر جلب الحالة";
            statusResult.style.display = "block";
        }
    });
}
