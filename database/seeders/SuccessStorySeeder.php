<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuccessStory;
use Illuminate\Support\Str;

class SuccessStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stories = [
            [
                'name' => 'John Doe',
                'career_path' => 'Software Engineer',
                'story' => 'John transitioned from a retail job to becoming a software engineer after completing an intensive coding bootcamp. His dedication to learning and building projects landed him a role at a top tech company.',
                'image' => 'images/success_stories/john_doe.jpg',
                'created_at' => now()->subDays(30),
            ],
            [
                'name' => 'Sarah Johnson',
                'career_path' => 'Data Scientist',
                'story' => 'Sarah leveraged her background in mathematics to pivot into data science. Her work on predictive models has been instrumental in her company’s growth.',
                'image' => 'images/success_stories/sarah_johnson.jpg',
                'created_at' => now()->subDays(28),
            ],
            [
                'name' => 'Michael Chen',
                'career_path' => 'Product Manager',
                'story' => 'Michael’s journey from a marketing assistant to a product manager involved mastering agile methodologies and user-centered design principles.',
                'image' => 'images/success_stories/michael_chen.jpg',
                'created_at' => now()->subDays(25),
            ],
            [
                'name' => 'Emily Davis',
                'career_path' => 'UX Designer',
                'story' => 'Emily’s passion for design and user experience led her to create intuitive interfaces, earning her recognition in the tech industry.',
                'image' => 'images/success_stories/emily_davis.jpg',
                'created_at' => now()->subDays(20),
            ],
            [
                'name' => 'Carlos Rivera',
                'career_path' => 'Cybersecurity Analyst',
                'story' => 'Carlos upskilled in cybersecurity through online courses, securing a role protecting sensitive data for a financial institution.',
                'image' => 'images/success_stories/carlos_rivera.jpg',
                'created_at' => now()->subDays(18),
            ],
            [
                'name' => 'Aisha Khan',
                'career_path' => 'Cloud Architect',
                'story' => 'Aisha’s expertise in cloud infrastructure helped her design scalable solutions for a global enterprise, earning her multiple certifications.',
                'image' => 'images/success_stories/aisha_khan.jpg',
                'created_at' => now()->subDays(15),
            ],
            [
                'name' => 'David Kim',
                'career_path' => 'DevOps Engineer',
                'story' => 'David automated deployment pipelines, reducing downtime and improving efficiency for his company’s software delivery.',
                'image' => 'images/success_stories/david_kim.jpg',
                'created_at' => now()->subDays(12),
            ],
            [
                'name' => 'Laura Martinez',
                'career_path' => 'AI Researcher',
                'story' => 'Laura’s research in machine learning algorithms contributed to breakthroughs in natural language processing.',
                'image' => 'images/success_stories/laura_martinez.jpg',
                'created_at' => now()->subDays(10),
            ],
            [
                'name' => 'James Patel',
                'career_path' => 'Full Stack Developer',
                'story' => 'James built end-to-end web applications, mastering both frontend and backend technologies to create seamless user experiences.',
                'image' => 'images/success_stories/james_patel.jpg',
                'created_at' => now()->subDays(8),
            ],
            [
                'name' => 'Sophie Brown',
                'career_path' => 'Digital Marketing Specialist',
                'story' => 'Sophie’s creative campaigns and data-driven strategies boosted her company’s online presence significantly.',
                'image' => 'images/success_stories/sophie_brown.jpg',
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($stories as $story) {
            SuccessStory::create($story);
        }
    }
}