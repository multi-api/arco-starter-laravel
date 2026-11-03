import DemoCard from '@/components/demo/demo-card';
import { Button } from '@/components/ui/button';
import { demoRequest } from '@/lib/demo-api';
import { useState } from 'react';

export default function CacheCard({ configured }: { configured: boolean }) {
    const [count, setCount] = useState<number | null>(null);

    const increment = async () => {
        const response = await demoRequest('/demo/counter');
        if (response.ok) setCount((await response.json()).count);
    };

    return (
        <DemoCard
            title="Cache"
            description="A counter kept in the shared cache; it survives page reloads and new Lambda instances."
            configured={configured}
        >
            <Button onClick={increment} disabled={!configured}>
                Add one
            </Button>
            {count !== null && <p>Counter: {count}</p>}
        </DemoCard>
    );
}
