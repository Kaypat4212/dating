<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@heartsconnect.com')->first()
            ?? User::first();

        if (! $admin) {
            $this->command->warn('No admin user found — skipping BlogPostSeeder.');
            return;
        }

        // Ensure a default category exists
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'relationship-tips'],
            [
                'name'        => 'Relationship Tips',
                'description' => 'Advice and insights for modern relationships.',
                'icon'        => '💡',
                'color'       => '#e11d48',
                'order'       => 1,
                'is_active'   => true,
            ]
        );

        $posts = [
            [
                'title'      => 'Why "We Met Online" Is No Longer Something to Be Embarrassed About',
                'slug'       => 'why-met-online-not-embarrassing',
                'excerpt'    => 'A decade ago, people made up cover stories. Today, nearly 40% of couples start online. Here\'s why the stigma has finally crumbled.',
                'tags'       => ['online dating', 'modern love', 'relationships'],
                'is_featured'=> true,
                'content'    => <<<HTML
<p>Remember when meeting someone on the internet meant you had to invent a story about a "chance encounter at a coffee shop"? Those days are gone — and honestly, good riddance.</p>

<h2>The numbers don't lie</h2>
<p>A 2023 Stanford study found that over <strong>39 % of heterosexual couples</strong> and more than <strong>60 % of same-sex couples</strong> in the US now meet online. Dating apps have quietly become the single most common way people find long-term partners — surpassing meeting through friends, work, or school.</p>

<h2>Why the shift happened</h2>
<p>Two things broke the stigma open: smartphones and COVID-19. Smartphones put dating apps in everyone's pocket without the "I have to open my laptop to check my profile" awkwardness. Then the pandemic removed every other option entirely. Suddenly <em>everyone</em> was matching on apps, and there was nobody left to judge.</p>

<h2>What it actually means for your relationship</h2>
<p>Research consistently shows that couples who meet online report <strong>equal or higher satisfaction</strong> than those who meet offline — and they tend to have slightly lower divorce rates. The intentionality of it matters: you're both there on purpose, often with filters already set for what you actually want.</p>

<h2>The one thing worth keeping</h2>
<p>The cover story era did teach us one useful thing: the story of <em>how</em> you met matters less than the story of <em>what you built together.</em> Own yours. "We matched on HeartConnect and it was the best swipe of my life" is a perfectly great story.</p>
HTML,
            ],
            [
                'title'      => 'The "Slow Burn" Is Having a Moment — And Therapists Are Thrilled',
                'slug'       => 'slow-burn-dating-trend-therapists',
                'excerpt'    => 'Gen Z is pushing back against instant-everything culture with intentional, unhurried dating. Here\'s what the slow-burn approach looks like in practice.',
                'tags'       => ['slow dating', 'gen z', 'intentional relationships'],
                'is_featured'=> false,
                'content'    => <<<HTML
<p>We live in a world of next-day delivery, two-minute AI responses, and same-day streaming. So it's striking that one of the fastest-growing dating trends right now is… slowing down.</p>

<h2>What is slow-burn dating?</h2>
<p>Slow burning is moving deliberately through the early stages of dating — spending more time getting to know someone before physical intimacy, before defining the relationship, before announcing anything on social media. It's less about playing games and more about <em>building a genuine foundation.</em></p>

<h2>Why therapists love it</h2>
<p>"Anxious attachment often thrives in fast-paced dating because ambiguity keeps the nervous system activated," says relationship therapist Dr. Priya Singh. "Slowing down gives both people time to self-regulate, communicate clearly, and actually figure out if they like each other — not just the dopamine rush."</p>

<h2>Practical ways to try it</h2>
<ul>
    <li><strong>Three dates before any label conversations.</strong> Give the connection room to breathe.</li>
    <li><strong>Voice notes over texts.</strong> Hearing a voice carries tone that emojis cannot.</li>
    <li><strong>One screen-free date.</strong> Phones in bags, full attention on the person in front of you.</li>
    <li><strong>Ask the weird questions early.</strong> "What does a Tuesday evening usually look like for you?" tells you more than "What's your love language?"</li>
</ul>

<h2>The bottom line</h2>
<p>Slow burning isn't about playing hard to get. It's about deciding that something worth having is worth taking the time to build properly. In a world that rewards speed, choosing patience might be the most radical dating move you can make.</p>
HTML,
            ],
            [
                'title'      => 'Long-Distance Relationships in 2026: Harder Than Ever, or Easier?',
                'slug'       => 'long-distance-relationships-2026',
                'excerpt'    => 'Video calls, shared playlists, and same-day flights changed everything. But so did burnout. A real look at LDRs right now.',
                'tags'       => ['long distance', 'LDR', 'modern relationships'],
                'is_featured'=> false,
                'content'    => <<<HTML
<p>Long-distance relationships are not new. What <em>is</em> new is the toolbox — and the particular brand of exhaustion that comes with it.</p>

<h2>The tech advantage</h2>
<p>Couples today can watch the same Netflix show simultaneously, do co-working video calls, send "thinking of you" voice notes that feel nothing like a cold text, and be on a plane in hours. The psychological distance of being far apart has genuinely shrunk.</p>

<h2>The hidden pressure</h2>
<p>But constant connectivity creates its own trap. When you <em>can</em> reach your partner 24/7, there's an invisible pressure that you <em>should</em> be in contact constantly. Missing a call or taking three hours to reply starts to feel like a small betrayal — even when it's just… life.</p>

<h2>What actually makes LDRs work in 2026</h2>
<ol>
    <li><strong>Agree on communication rhythms upfront.</strong> Not "we'll talk whenever" — but actual shared expectations. Daily check-in? Weekly video date? Be specific.</li>
    <li><strong>Have a timeline.</strong> LDRs with no end date have a much higher failure rate. You don't need a precise date — but you need a shared direction.</li>
    <li><strong>Protect in-person time fiercely.</strong> When you're together, be <em>together</em>. Put the documenting-for-Instagram on pause.</li>
    <li><strong>Normalize the hard days.</strong> Missing someone hurts. Feeling frustrated doesn't mean the relationship is broken.</li>
</ol>

<h2>The verdict</h2>
<p>For couples with strong communication, a clear timeline, and the ability to tolerate uncertainty — 2026 is actually a reasonable time to do long distance. The tools are there. The question is whether the intention is too.</p>
HTML,
            ],
            [
                'title'      => '5 Green Flags You Should Actually Be Looking For (Not Just the Absence of Red Ones)',
                'slug'       => '5-green-flags-dating',
                'excerpt'    => 'We are so trained to spot red flags that we miss the signs a relationship is genuinely healthy. Here are five green flags to watch for.',
                'tags'       => ['green flags', 'healthy relationships', 'dating advice'],
                'is_featured'=> true,
                'content'    => <<<HTML
<p>The "red flag" discourse has dominated dating culture for years — and for good reason. Recognising harmful patterns early matters. But there's a quieter skill that matters just as much: recognising what <em>good</em> actually looks like.</p>

<h2>1. They're comfortable with your "no"</h2>
<p>A person who respects your limits without sulking, punishing, or needing extensive explanation is rare and precious. Whether it's "I don't want to share my location" or "I need this weekend to myself" — how someone handles your "no" tells you almost everything about how they'll treat you under stress.</p>

<h2>2. They're curious about you, not just impressed by you</h2>
<p>Some people want an audience. A green flag is someone who asks follow-up questions, remembers what you said last week, and genuinely wants to understand your world — not just catalogue your achievements.</p>

<h2>3. They talk about other people with basic decency</h2>
<p>How a person speaks about their exes, their friends, their family, and strangers in service roles is a reliable preview of how they'll speak about <em>you</em> one day. Chronic contempt doesn't stay targeted.</p>

<h2>4. They can disagree without it becoming a fight</h2>
<p>Healthy couples don't agree on everything. What they can do is hold different perspectives, express them, and move through it without it threatening the relationship. Watch for someone who says "I see it differently" instead of "you're wrong."</p>

<h2>5. Their actions and words are the same thing</h2>
<p>They said they'd call — and they called. They said the restaurant at 7 — and they were there at 6:55. Consistency isn't exciting to talk about, but it is the foundation that everything else is built on. Reliability is a love language.</p>
HTML,
            ],
            [
                'title'      => 'How to Actually Have the "What Are We?" Conversation Without Dreading It',
                'slug'       => 'how-to-have-the-what-are-we-conversation',
                'excerpt'    => 'The DTR talk does not have to be a high-stakes confrontation. With the right framing, it is just two adults figuring out if they want the same thing.',
                'tags'       => ['DTR', 'commitment', 'relationship talks'],
                'is_featured'=> false,
                'content'    => <<<HTML
<p>"What are we?" — four words that have sent countless people spiralling into a pre-conversation anxiety spiral. But here's the truth: it doesn't have to be terrifying. In fact, it's one of the most useful conversations you can have.</p>

<h2>Why we dread it</h2>
<p>The fear isn't really about the conversation — it's about the answer. We dread it because we think we might hear something we don't want to hear. And that is a valid fear! But avoiding the talk doesn't change the answer. It just delays it while you invest more time and emotion into something undefined.</p>

<h2>Reframe it before you have it</h2>
<p>Instead of "I need to have *the talk*," try entering it as information-gathering. You're not issuing an ultimatum. You're checking to see if you're on the same page. Low pressure framing: <em>"Hey, I like spending time with you and I wanted to check in about where your head is at — no pressure either way."</em></p>

<h2>Pick the right moment</h2>
<p>Not over text. Not right after sex. Not when either of you is tired or stressed. A quiet, neutral moment — a walk, a relaxed dinner at home — where you both feel comfortable and unhurried.</p>

<h2>Know your own answer first</h2>
<p>Before asking what <em>they</em> want, know what you want. Are you looking for something serious? Are you open to casual? Having clarity about your own position means you can actually listen to theirs instead of trying to figure out both answers at once.</p>

<h2>Accept the outcome gracefully</h2>
<p>If you're not aligned, that's genuinely useful information. It might sting, but it means you can redirect your energy somewhere it has a future. Compatibility isn't something you convince someone into — and you wouldn't want to.</p>

<p>The "what are we" conversation, at its core, is just two people deciding whether to build something together. That's not scary. That's just honest.</p>
HTML,
            ],
        ];

        foreach ($posts as $data) {
            BlogPost::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'author_id'      => $admin->id,
                    'category_id'    => $category->id,
                    'title'          => $data['title'],
                    'slug'           => $data['slug'],
                    'excerpt'        => $data['excerpt'],
                    'content'        => $data['content'],
                    'tags'           => $data['tags'],
                    'status'         => 'published',
                    'published_at'   => now()->subDays(rand(1, 14)),
                    'allow_comments' => true,
                    'is_featured'    => $data['is_featured'],
                    'views_count'    => rand(40, 480),
                    'likes_count'    => rand(5, 60),
                    'comments_count' => 0,
                ]
            );
        }

        $this->command->info('✅ 5 blog posts seeded.');
    }
}
