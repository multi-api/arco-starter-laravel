import DemoCard from '@/components/demo/demo-card';
import { Button } from '@/components/ui/button';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function DatabaseCard({ configured }: { configured: boolean }) {
    const { auth } = usePage<SharedData>().props;

    return (
        <DemoCard
            title="Database and login"
            description="Register and sign in with the starter kit's auth pages, stored in your managed database."
            configured={configured}
        >
            {!configured && <p>Add a managed database and choose Save and deploy to turn on login.</p>}
            {/* The auth routes only exist with a database, so never build their URLs without one. */}
            {configured &&
                (auth.user ? (
                    <div className="flex flex-wrap items-center gap-3">
                        <p>Signed in as {auth.user.email}.</p>
                        <Button asChild variant="outline">
                            <Link href={route('dashboard')}>Dashboard</Link>
                        </Button>
                    </div>
                ) : (
                    <div className="flex gap-2">
                        <Button asChild>
                            <Link href={route('register')}>Register</Link>
                        </Button>
                        <Button asChild variant="outline">
                            <Link href={route('login')}>Log in</Link>
                        </Button>
                    </div>
                ))}
        </DemoCard>
    );
}
