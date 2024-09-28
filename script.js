const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');
const postsContainer = document.querySelector('.posts');

prevBtn.addEventListener('click', () => {
    postsContainer.scrollBy({
        left: -300,
        behavior: 'smooth'
    });
});

nextBtn.addEventListener('click', () => {
    postsContainer.scrollBy({
        left: 300,
        behavior: 'smooth'
    });
});
